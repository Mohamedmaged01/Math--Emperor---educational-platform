<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminStore;
use App\Support\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const BAD_LOGIN = 'بيانات الدخول غير صحيحة. تحقق من اسم المستخدم والصلاحية.';

    public function __construct(private AdminStore $store) {}

    /** POST /login — role-scoped sign-in for the student gate and the imperial console */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:190'],
            'password' => ['required', 'string', 'max:190'],
            'remember' => ['boolean'],
            'role' => ['required', 'in:STUDENT,STAFF'],
        ], [
            'identifier.required' => 'أدخل اسم المستخدم وكلمة المرور للمتابعة.',
            'password.required' => 'أدخل اسم المستخدم وكلمة المرور للمتابعة.',
        ]);

        $id = Str::lower(trim($data['identifier']));
        $throttle = 'login:'.$id.'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttle, 5)) {
            $secs = RateLimiter::availableIn($throttle);

            return response()->json(['ok' => false, 'error' => "محاولات كثيرة — حاول مجددًا بعد {$secs} ثانية."], 429);
        }

        $user = User::whereRaw('lower(email) = ?', [$id])->first();
        // uniform error — no user enumeration (documented behaviour of the original)
        if (! $user || $user->role !== $data['role'] || ! Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($throttle, 60);

            return response()->json(['ok' => false, 'error' => self::BAD_LOGIN], 422);
        }

        RateLimiter::clear($throttle);
        Auth::login($user, (bool) ($data['remember'] ?? false));
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'redirect' => $this->home($user)]);
    }

    /** POST /register — new student account (same validation rules as validateRegistration) */
    public function register(Request $request): JsonResponse
    {
        $in = $request->only(['name', 'phone', 'grade', 'email', 'password']);
        $name = preg_replace('/\s+/u', ' ', trim((string) ($in['name'] ?? '')));
        $parts = $name === '' ? [] : explode(' ', $name);
        if (count($parts) < 4 || collect($parts)->contains(fn ($p) => mb_strlen($p) < 2)) {
            return $this->regError('name', 'اكتب اسمك الرباعي كاملًا (أربعة أسماء، كل اسم حرفان على الأقل).');
        }

        $phone = $this->normalizePhone((string) ($in['phone'] ?? ''));
        if (! preg_match('/^05\d{8}$/', $phone)) {
            return $this->regError('phone', 'رقم الجوال غير صحيح — اكتبه بصيغة 05xxxxxxxx أو +9665xxxxxxxx.');
        }

        if (! in_array($in['grade'] ?? '', Content::secondaryGrades(), true)) {
            return $this->regError('grade', 'اختر المرحلة الدراسية من القائمة (الأول/الثاني/الثالث الثانوي).');
        }

        $email = Str::lower(trim((string) ($in['email'] ?? '')));
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->regError('email', 'البريد الإلكتروني غير صحيح — مثال: name@example.com');
        }

        if (mb_strlen((string) ($in['password'] ?? '')) < 6) {
            return $this->regError('password', 'كلمة المرور يجب أن تكون ٦ أحرف أو أكثر.');
        }

        if (User::whereRaw('lower(email) = ?', [$email])->exists()) {
            return $this->regError('email', 'هذا البريد مسجّل مسبقًا — جرّب تسجيل الدخول أو بريدًا آخر.');
        }

        $user = User::create([
            'name' => $name, 'email' => $email, 'password' => $in['password'],
            'role' => User::STUDENT, 'phone' => $phone, 'grade' => $in['grade'],
        ]);
        $user->seedDefaultPortal();

        // surface the new account in the imperial "students" dataset
        $this->store->insert('students', 'students', [
            'name' => $name, 'email' => $email, 'phone' => $phone, 'grade' => $in['grade'],
            'plan' => 'باقة مجانية', 'simsUsed' => 0, 'simsLimit' => 3, 'simUnlimited' => false,
            'vault' => 'محظور', 'live' => 'مقيد', 'aiLimit' => 5, 'status' => 'نشط',
            'enrolled' => now()->toDateString(),
        ]);

        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'redirect' => route('student.portal')]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /** POST /password/request — step 1: email a 6-digit code to the account's recovery address */
    public function requestReset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:190'],
            'role' => ['required', 'in:STUDENT,STAFF'],
        ], ['identifier.required' => 'اكتب اسم المستخدم أو البريد الإلكتروني المعتمد لحسابك.']);

        $throttle = 'pwreset:'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttle, 5)) {
            return response()->json(['ok' => false, 'error' => 'محاولات كثيرة — حاول لاحقًا.'], 429);
        }
        RateLimiter::hit($throttle, 300);

        $user = $this->findForReset($data['identifier'], $data['role']);
        $isStaff = $data['role'] === User::STAFF;
        $masked = null;

        if ($user) {
            $to = $isStaff ? ($user->recovery_email ?: $user->email) : $user->email;
            $code = (string) random_int(100000, 999999);
            Cache::put($this->resetKey($user), Hash::make($code), now()->addMinutes(15));
            Mail::raw(
                "رمز تحديث كلمة المرور لمنصة إمبراطور الرياضيات: {$code}\nصالح لمدة ١٥ دقيقة. إن لم تطلب ذلك فتجاهل هذه الرسالة.",
                fn ($m) => $m->to($to)->subject('رمز تحديث كلمة المرور — منصة الإمبراطور')
            );
            $masked = $this->maskEmail($to);
        }

        $this->store->note(
            'طلب استعادة كلمة مرور', 'security',
            $masked ? "رابط تحديث أُرسل إلى {$masked} — ".($isStaff ? 'إداري' : 'طالب') : 'إدخال مستخدم غير موجود (رد عام بدون كشف)'
        );

        return response()->json(['ok' => true, 'maskedEmail' => $masked]);
    }

    /** POST /password/reset — step 2: verify the code and store the new (hashed) password */
    public function reset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:190'],
            'role' => ['required', 'in:STUDENT,STAFF'],
            'code' => ['nullable', 'string', 'max:12'],
            'password' => ['nullable', 'string', 'max:190'],
        ]);
        $isStaff = $data['role'] === User::STAFF;
        $user = $this->findForReset($data['identifier'], $data['role']);

        if (! $user) {
            return response()->json(['ok' => false, 'error' => $isStaff
                ? 'تعذر إكمال الطلب — تحقق من بياناتك ثم حاول مجددًا.'
                : 'تعذر إكمال الطلب — تحقق من بريدك ثم حاول مجددًا.'], 422);
        }
        if (mb_strlen((string) ($data['password'] ?? '')) < 8) {
            return response()->json(['ok' => false, 'error' => 'كلمة المرور الجديدة يجب أن تكون ٨ أحرف أو أكثر.'], 422);
        }
        $hash = Cache::get($this->resetKey($user));
        if (! $hash || ! Hash::check(trim((string) ($data['code'] ?? '')), $hash)) {
            return response()->json(['ok' => false, 'error' => 'رمز التحقق غير صحيح أو منتهي الصلاحية — اطلب رمزًا جديدًا.'], 422);
        }

        $user->update(['password' => $data['password']]);
        Cache::forget($this->resetKey($user));
        $this->store->note(
            $isStaff ? 'تحديث كلمة مرور إدارية' : 'تحديث كلمة مرور طالب', 'security',
            'حساب: '.Str::lower(trim($data['identifier'])).' — تم التحديث بالبصمة'
        );

        return response()->json(['ok' => true]);
    }

    /* ------------------------------ helpers ------------------------------ */

    private function home(User $user): string
    {
        return $user->isStaff() ? route('imperial.show') : route('student.portal');
    }

    private function regError(string $field, string $error): JsonResponse
    {
        return response()->json(['ok' => false, 'field' => $field, 'error' => $error], 422);
    }

    private function normalizePhone(string $phone): string
    {
        $p = preg_replace('/[\s-]/', '', $phone);
        $p = preg_replace('/^\+?966/', '0', $p);

        return preg_replace('/^00966/', '0', $p);
    }

    private function findForReset(string $identifier, string $role): ?User
    {
        $id = Str::lower(trim($identifier));
        $q = User::where('role', $role);

        return $role === User::STAFF
            ? $q->where(fn ($w) => $w->whereRaw('lower(email) = ?', [$id])->orWhereRaw('lower(recovery_email) = ?', [$id]))->first()
            : $q->whereRaw('lower(email) = ?', [$id])->first();
    }

    private function resetKey(User $user): string
    {
        return 'pw-reset-code:'.$user->id;
    }

    private function maskEmail(string $email): string
    {
        [$user, $domain] = array_pad(explode('@', $email, 2), 2, '');

        return mb_substr($user, 0, 1).str_repeat('*', max(2, mb_strlen($user) - 1)).'@'.$domain;
    }
}
