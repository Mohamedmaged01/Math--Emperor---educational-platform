@php
    $grades = \App\Support\Content::secondaryGrades();
    $inputCls = 'h-12 w-full rounded-xl border border-border bg-surface-2 px-4 pe-4 ps-11 text-small font-bold text-foreground placeholder:text-foreground-3 transition-all focus:border-primary-400 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary-500/25';
    $regCls = 'h-11.5 w-full rounded-xl border bg-surface-2 px-3.5 text-small font-bold text-foreground placeholder:text-foreground-3 focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20';
@endphp
<div x-data="authPortal" @emperor:open-student.window="openModal('student')" @emperor:open-imperial.window="openModal('imperial')" @emperor:close-windows.window="modal = 'closed'">
    <x-modal show="modal !== 'closed'" close="openModal('closed')" size="sm">
        <div class="anim-rise">
            {{-- brandhead --}}
            <div class="relative -mx-6 -mt-6 mb-6 overflow-hidden rounded-t-2xl px-6 pt-7 pb-6 text-center sm:-mx-7 sm:-mt-7">
                <div class="absolute inset-0 bg-gradient-to-b" aria-hidden="true"
                    :class="isImperial ? 'from-primary-700 via-primary-800 to-primary-900' : 'from-secondary-600 via-secondary-700 to-[#0c4f47]'"></div>
                <div class="absolute inset-0 opacity-15" aria-hidden="true"
                    style="background-image: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.35) 0, transparent 40%), radial-gradient(circle at 80% 60%, rgba(255,255,255,0.25) 0, transparent 35%)"></div>
                <div class="relative">
                    <span class="anim-pop mx-auto grid size-16 place-items-center rounded-3xl bg-white/14 ring-1 ring-white/30 backdrop-blur">
                        <span x-show="isImperial"><x-i name="crown" class="size-8 text-amber-300" stroke-width="2.2" /></span>
                        <span x-show="!isImperial"><x-i name="graduation-cap" class="size-8 text-white" stroke-width="2.2" /></span>
                    </span>
                    <h2 class="mt-4 text-title text-white" x-text="isImperial ? 'لوحة التحكم الإمبراطورية المتكاملة' : 'بوابة دخول الطلاب • منصة الإمبراطور'"></h2>
                    <p class="mt-1.5 text-xs font-bold leading-relaxed text-white/80"
                        x-text="isImperial ? 'حساب بوابة تسجيل الدخول الآمنة للإدارة العليا وحساب المشرفين والمعلمين المعتمدين' : 'تسجيل الدخول ومتابعة درجات المحاكاة وتجميعاتك'"></p>
                    <span class="num mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/14 px-3.5 py-1.5 text-[10px] font-black tracking-wide text-white/90 ring-1 ring-white/20">
                        <x-i name="key-round" class="size-3.5 text-amber-300" />
                        <span x-text="isImperial ? 'نظام المصادقة والتشفير المتقدم ١٤٤٦هـ' : 'STUDENT SECURE ACCESS · 1446H'"></span>
                    </span>
                </div>
            </div>

            {{-- student quick CTA (login mode only) --}}
            <button type="button" x-show="!isImperial && mode === 'login' && !resetStep" @click="quickFill()"
                class="group mb-4 flex w-full items-center gap-3 rounded-2xl border border-secondary-100 bg-secondary-soft p-3.5 text-start transition-all hover:border-secondary-500/40 hover:shadow-md">
                <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-white/80 text-secondary shadow-xs"><x-i name="zap" class="size-5" /></span>
                <span class="flex-1">
                    <span class="block text-small font-black text-foreground">جرّب المنصة كطالب متفوق بضغطة زر</span>
                    <span class="block text-[10px] font-bold text-foreground-3">تعبئة الحساب التجريبي: student@emperor.sa / Emperor@2026</span>
                </span>
                <span x-show="quickFilled"><x-i name="check-circle-2" class="size-5 shrink-0 text-success" /></span>
            </button>

            {{-- mode switch (students) --}}
            <div x-show="!isImperial && !resetStep" class="mb-4 grid grid-cols-2 gap-1 rounded-xl border border-border bg-surface-2 p-1" role="tablist" aria-label="وضع البوابة">
                @foreach (['login' => 'تسجيل الدخول', 'register' => 'حساب جديد'] as $m => $label)
                    <button type="button" role="tab" :aria-selected="mode === '{{ $m }}'" @click="mode = '{{ $m }}'; error = null; regError = null"
                        :class="mode === '{{ $m }}' ? 'bg-gradient-to-l from-secondary-500 to-secondary-700 text-white shadow-md shadow-secondary-500/25' : 'text-foreground-2 hover:text-secondary'"
                        class="rounded-lg py-2.5 text-xs font-black transition-all duration-200">{{ $label }}</button>
                @endforeach
            </div>

            {{-- imperial account cards --}}
            <div x-show="isImperial && !resetStep && !imperialSelection" class="mb-4">
                <p class="mb-3 text-center text-xs font-black text-foreground">اختر نوع حسابك لمتابعة الدخول:</p>
                <div class="grid gap-2.5 sm:grid-cols-2">
                    <button type="button" @click="pickImperial('admin')"
                        class="group rounded-2xl border border-amber-200 bg-amber-50/80 p-3.5 text-start transition-all hover:border-amber-400 hover:shadow-md dark:border-warning/25 dark:bg-warning-soft/60">
                        <span class="flex items-center gap-2 text-xs font-black text-foreground">
                            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-amber-300 to-amber-500 text-white shadow-xs transition-transform duration-300 group-hover:scale-110"><x-i name="crown" class="size-4" /></span>
                            الإدارة العليا
                        </span>
                        <span class="mt-1.5 block text-[10px] font-bold leading-relaxed text-foreground-3">(كامل الصلاحيات) · حساب تجريبي: <span class="num" dir="ltr">admin / Emperor@2026</span></span>
                    </button>
                    <button type="button" @click="pickImperial('teacher')"
                        class="group rounded-2xl border border-primary-100 bg-primary-soft/70 p-3.5 text-start transition-all hover:border-primary-300 hover:shadow-md">
                        <span class="flex items-center gap-2 text-xs font-black text-foreground">
                            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-primary-400 to-primary-700 text-white shadow-xs transition-transform duration-300 group-hover:scale-110"><x-i name="graduation-cap" class="size-4" /></span>
                            المعلم والمشرف الأكاديمي
                        </span>
                        <span class="mt-1.5 block text-[10px] font-bold leading-relaxed text-foreground-3">البريد المعتمد: <span class="num" dir="ltr">fahad.zahrani@emperor.edu.sa</span></span>
                    </button>
                </div>
            </div>

            {{-- ================= password reset (staff + students) ================= --}}
            <form x-show="resetStep" x-cloak @submit.prevent="resetStep === 'request' ? sendResetCode() : applyNewPassword()" class="space-y-4">
                <div class="flex items-center gap-3 rounded-2xl border border-primary-100 bg-primary-soft/60 p-4">
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-surface text-primary shadow-xs"><x-i name="mail" class="size-5.5" /></span>
                    <div>
                        <p class="text-small font-black text-foreground" x-text="'استعادة كلمة المرور ' + (isImperial ? 'للإدارة والمعلمين' : 'للطالب')"></p>
                        <p class="text-[10px] font-bold text-foreground-3" x-text="resetStep === 'request'
                            ? (isImperial ? 'سيرسل رمز تحديث لكلمة المرور الجديدة إلى بريدك الإلكتروني المعتمد' : 'اكتب بريد حساب الطالب لنرسل رمز تحديث كلمة المرور الجديدة')
                            : (maskedEmail ? 'أُرسل رمز التحقق إلى ' + maskedEmail : 'إن كان الحساب مسجلًا لدينا فستصلك رسالة التحديث عبر البريد المرتبط به')"></p>
                    </div>
                </div>

                <template x-if="resetStep === 'request'">
                    <div class="space-y-4">
                        <label class="block">
                            <span class="mb-1.5 flex items-center gap-1.5 text-xs font-black text-foreground-2">
                                <span x-show="isImperial"><x-i name="user-round" class="size-4 text-primary" /></span>
                                <span x-show="!isImperial"><x-i name="mail" class="size-4 text-secondary" /></span>
                                <span x-text="isImperial ? 'اسم المستخدم أو البريد الإلكتروني المعتمد' : 'البريد الإلكتروني لحساب الطالب'"></span>
                            </span>
                            <input x-model="resetId" required autocomplete="username" :type="isImperial ? 'text' : 'email'" :dir="isImperial ? 'rtl' : 'ltr'"
                                :placeholder="isImperial ? 'admin أو fahad.zahrani@emperor.edu.sa' : 'student@example.com'" class="{{ $inputCls }}" />
                        </label>
                        <button type="submit" :disabled="busy"
                            class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 text-small font-black text-white shadow-lg shadow-primary-500/25 transition-all hover:brightness-110 disabled:opacity-60">
                            <span x-show="busy" class="inline-flex items-center gap-2"><x-i name="loader-2" class="size-4.5 animate-spin" />جارٍ إرسال الرمز…</span>
                            <span x-show="!busy" class="inline-flex items-center gap-2"><x-i name="send" class="size-4.5 -scale-x-100" />إرسال رمز التحديث عبر البريد الإلكتروني</span>
                        </button>
                    </div>
                </template>

                <template x-if="resetStep === 'sent'">
                    <div class="space-y-4">
                        <label class="block">
                            <span class="mb-1.5 block text-[11px] font-black text-foreground-2">رمز التحقق المرسل إلى بريدك *</span>
                            <input x-model="resetCode" required inputmode="numeric" maxlength="6" dir="ltr" placeholder="••••••"
                                class="num {{ $inputCls }} ps-4 text-center tracking-[0.5em]" />
                        </label>
                        <label class="block">
                            <span class="mb-1.5 block text-[11px] font-black text-foreground-2">كلمة المرور الجديدة *</span>
                            <div class="relative">
                                <input x-model="newPass" required :type="showPass ? 'text' : 'password'" placeholder="٨ أحرف أو أكثر" class="{{ $inputCls }} pe-11" />
                                <button type="button" @click="showPass = !showPass" :aria-label="showPass ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور'"
                                    class="absolute inset-y-0 end-3 grid w-8 place-items-center text-foreground-3 transition-colors hover:text-primary">
                                    <span x-show="showPass"><x-i name="eye-off" class="size-4.5" /></span>
                                    <span x-show="!showPass"><x-i name="eye" class="size-4.5" /></span>
                                </button>
                            </div>
                        </label>
                        <button type="submit" :disabled="busy"
                            class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-secondary-500 to-secondary-700 text-small font-black text-white shadow-lg shadow-secondary-500/25 transition-all hover:brightness-110 disabled:opacity-60">
                            <span x-show="busy" class="inline-flex items-center gap-2"><x-i name="loader-2" class="size-4.5 animate-spin" />جارٍ تحديث كلمة المرور…</span>
                            <span x-show="!busy" class="inline-flex items-center gap-2"><x-i name="key-round" class="size-4.5" />تحديث كلمة المرور الجديدة</span>
                        </button>
                    </div>
                </template>

                <p x-show="resetError" x-cloak role="alert" class="anim-rise flex items-start gap-2 rounded-xl border border-danger/25 bg-danger-soft px-3.5 py-2.5 text-xs font-bold text-danger">
                    <x-i name="shield-check" class="mt-0.5 size-4 shrink-0" /><span x-text="resetError"></span>
                </p>

                <button type="button" @click="resetStep = null; resetError = null; maskedEmail = null; newPass = ''; resetCode = ''"
                    class="mx-auto flex items-center gap-1.5 text-[11px] font-black text-foreground-3 transition-colors hover:text-primary">عودة لتسجيل الدخول</button>
            </form>

            {{-- ================= student registration ================= --}}
            <form x-show="!resetStep && !isImperial && mode === 'register'" x-cloak @submit.prevent="submitRegister()" class="space-y-3.5" novalidate>
                @foreach ([['name', 'الاسم رباعي *', 'text', 'الاسم الأول · الأب · الجد · العائلة', null], ['phone', 'رقم الجوال *', 'text', '05xxxxxxxx', 'ltr']] as [$key, $label, $type, $ph, $dir])
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-black text-foreground-2">{{ $label }}</span>
                        <input type="{{ $type }}" x-model="reg.{{ $key }}" @input="regError = null" placeholder="{{ $ph }}" @if ($dir) dir="{{ $dir }}" @endif
                            :class="regError?.field === '{{ $key }}' ? 'border-danger/50' : 'border-border'" class="{{ $regCls }} {{ $dir === 'ltr' ? 'text-left' : '' }}" />
                    </label>
                @endforeach
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-black text-foreground-2">المرحلة الدراسية *</span>
                    <select x-model="reg.grade" @change="regError = null" :class="regError?.field === 'grade' ? 'border-danger/50' : 'border-border'" class="{{ $regCls }} appearance-none">
                        <option value="">اختر المرحلة الدراسية…</option>
                        @foreach ($grades as $g)
                            <option value="{{ $g }}">{{ $g }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-black text-foreground-2">البريد الإلكتروني *</span>
                    <input type="email" x-model="reg.email" @input="regError = null" placeholder="name@example.com" dir="ltr"
                        :class="regError?.field === 'email' ? 'border-danger/50' : 'border-border'" class="{{ $regCls }} text-left" />
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-black text-foreground-2">كلمة المرور *</span>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" x-model="reg.password" @input="regError = null" placeholder="٦ أحرف أو أكثر"
                            :class="regError?.field === 'password' ? 'border-danger/50' : 'border-border'" class="{{ $regCls }} pe-11" />
                        <button type="button" @click="showPass = !showPass" :aria-label="showPass ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور'"
                            class="absolute inset-y-0 end-2.5 grid w-8 place-items-center text-foreground-3 transition-colors hover:text-primary">
                            <span x-show="showPass"><x-i name="eye-off" class="size-4.5" /></span>
                            <span x-show="!showPass"><x-i name="eye" class="size-4.5" /></span>
                        </button>
                    </div>
                </label>

                <p x-show="regError" x-cloak role="alert" class="anim-rise flex items-start gap-2 rounded-xl border border-danger/25 bg-danger-soft px-3.5 py-2.5 text-xs font-bold text-danger">
                    <x-i name="shield-check" class="mt-0.5 size-4 shrink-0" /><span x-text="regError?.msg"></span>
                </p>

                <button type="submit" :disabled="busy"
                    class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-secondary-500 to-secondary-700 text-small font-black text-white shadow-lg shadow-secondary-500/25 transition-all duration-200 hover:brightness-110 active:scale-[0.99] disabled:opacity-60">
                    <span x-show="busy" class="inline-flex items-center gap-2"><x-i name="loader-2" class="size-4.5 animate-spin" />جارٍ إنشاء حسابك…</span>
                    <span x-show="!busy" class="inline-flex items-center gap-2"><x-i name="graduation-cap" class="size-4.5" />إنشاء الحساب والدخول</span>
                </button>

                <p class="text-center text-[10px] font-bold leading-relaxed text-foreground-3">
                    بإنشائك الحساب فأنت توافق على
                    <button type="button" @click="$dispatch('emperor:open-legal', 'terms')" class="font-black text-primary hover:underline">الشروط والأحكام</button>
                    و<button type="button" @click="$dispatch('emperor:open-legal', 'privacy')" class="font-black text-primary hover:underline">سياسة الخصوصية</button>.
                </p>
            </form>

            {{-- ================= login ================= --}}
            <form x-show="!resetStep && ((isImperial && imperialSelection) || (!isImperial && mode === 'login'))" @submit.prevent="submit()" class="space-y-4">
                <div x-show="isImperial && imperialSelection" class="flex items-center justify-between gap-3 rounded-xl border border-primary-100 bg-primary-soft/60 px-3.5 py-2.5">
                    <span class="inline-flex min-w-0 items-center gap-2 text-xs font-black text-foreground">
                        <span x-show="imperialSelection === 'admin'"><x-i name="crown" class="size-4 shrink-0 text-amber-500" /></span>
                        <span x-show="imperialSelection === 'teacher'"><x-i name="graduation-cap" class="size-4 shrink-0 text-primary" /></span>
                        <span class="truncate" x-text="imperialSelection === 'admin' ? 'تسجيل الدخول — الإدارة العليا (كامل الصلاحيات)' : 'تسجيل الدخول — المعلم والمشرف الأكاديمي'"></span>
                    </span>
                    <button type="button" @click="imperialSelection = null; identifier = ''; password = ''; error = null"
                        class="shrink-0 text-[10px] font-black text-primary transition-colors hover:text-primary-hover hover:underline">تغيير نوع الحساب</button>
                </div>

                <label class="block">
                    <span class="mb-1.5 flex items-center gap-1.5 text-xs font-black text-foreground-2">
                        <x-i name="user-round" class="size-4 text-primary" />
                        <span x-text="isImperial ? 'اسم المستخدم أو البريد الإلكتروني *' : 'البريد الإلكتروني:'"></span>
                    </span>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 start-3.5 grid place-items-center text-foreground-3" aria-hidden="true"><x-i name="user-round" class="size-4.5" /></span>
                        <input x-model="identifier" required autocomplete="username" :placeholder="isImperial ? 'admin أو بريدك المعتمد' : 'student@emperor.sa'" class="{{ $inputCls }}" />
                    </div>
                </label>

                <label class="block">
                    <span class="mb-1.5 flex items-center gap-1.5 text-xs font-black text-foreground-2">
                        <x-i name="lock-keyhole" class="size-4 text-primary" />
                        <span x-text="isImperial ? 'كلمة المرور *' : 'كلمة المرور:'"></span>
                    </span>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 start-3.5 grid place-items-center text-foreground-3" aria-hidden="true"><x-i name="lock-keyhole" class="size-4.5" /></span>
                        <input x-model="password" required :type="showPass ? 'text' : 'password'" autocomplete="current-password" placeholder="••••••••" class="{{ $inputCls }} pe-11" />
                        <button type="button" @click="showPass = !showPass" :aria-label="showPass ? 'إخفاء كلمة المرور' : 'إظهار كلمة المرور'"
                            class="absolute inset-y-0 end-3 grid w-8 place-items-center text-foreground-3 transition-colors hover:text-primary">
                            <span x-show="showPass"><x-i name="eye-off" class="size-4.5" /></span>
                            <span x-show="!showPass"><x-i name="eye" class="size-4.5" /></span>
                        </button>
                    </div>
                </label>

                <div class="-mt-1.5 text-left">
                    <button type="button" @click="startReset()" class="inline-flex items-center gap-1.5 text-[11px] font-black text-primary transition-colors hover:text-primary-hover hover:underline">
                        <x-i name="key-round" class="size-3.5" />
                        نسيت كلمة المرور؟
                    </button>
                </div>

                <label class="flex cursor-pointer select-none items-center gap-2.5">
                    <span :class="remember ? 'border-primary-500 bg-primary-500' : 'border-border-strong bg-surface'" class="grid size-5 place-items-center rounded-md border-2 transition-all duration-200" aria-hidden="true">
                        <span x-show="remember"><x-i name="check-circle-2" class="size-3.5 text-white" stroke-width="3.4" /></span>
                    </span>
                    <input type="checkbox" x-model="remember" class="sr-only" />
                    <span class="text-xs font-black text-foreground-2">تذكر بياناتي في هذا المتصفح</span>
                </label>

                <p x-show="resetDone" x-cloak role="status" class="anim-rise flex items-center gap-2 rounded-xl border border-success/30 bg-success-soft px-3.5 py-2.5 text-xs font-black text-success">
                    <x-i name="check-circle-2" class="size-4.5 shrink-0" />
                    تم تحديث كلمة المرور بنجاح — سجّل دخولك الآن بكلمة المرور الجديدة.
                </p>

                <p x-show="error" x-cloak role="alert" class="anim-rise flex items-start gap-2 overflow-hidden rounded-xl border border-danger/25 bg-danger-soft px-3.5 py-2.5 text-xs font-bold text-danger">
                    <x-i name="shield-check" class="mt-0.5 size-4 shrink-0" /><span x-text="error"></span>
                </p>

                <button type="submit" :disabled="busy"
                    :class="isImperial ? 'bg-gradient-to-l from-primary-500 to-primary-700 shadow-primary-500/25' : 'bg-gradient-to-l from-secondary-500 to-secondary-700 shadow-secondary-500/25'"
                    class="flex h-12 w-full items-center justify-center gap-2 rounded-xl text-small font-black text-white shadow-lg transition-all duration-200 hover:brightness-110 active:scale-[0.99] disabled:opacity-60">
                    <span x-show="busy" class="inline-flex items-center gap-2"><x-i name="loader-2" class="size-4.5 animate-spin" />جارٍ التحقق الآمن…</span>
                    <span x-show="!busy && isImperial" class="inline-flex items-center gap-2"><x-i name="crown" class="size-4.5" />الدخول للوحة القيادة</span>
                    <span x-show="!busy && !isImperial" class="inline-flex items-center gap-2"><x-i name="graduation-cap" class="size-4.5" />دخول بوابة الطلاب</span>
                </button>
            </form>

            {{-- security strip --}}
            <div class="mt-5 flex items-center justify-center gap-2 rounded-xl bg-surface-2 px-3 py-2.5 text-[10px] font-black text-foreground-3">
                <x-i name="shield-check" class="size-4 text-success" />
                <span class="num">اتصال SSL مشفّر</span>
                <span aria-hidden="true">·</span>
                <span x-text="isImperial ? 'موثق بجهة الإدارة العليا' : 'جلسة آمنة محمية بالخادم'"></span>
            </div>

            {{-- home --}}
            <button type="button" @click="openModal('closed')" aria-label="الصفحة الرئيسية"
                class="group mx-auto mt-3.5 flex items-center gap-2 rounded-xl px-4 py-2 text-[11px] font-black text-foreground-3 transition-colors hover:text-primary">
                <span class="grid size-7 place-items-center rounded-lg bg-surface-2 text-foreground-3 shadow-xs transition-colors group-hover:bg-primary-soft group-hover:text-primary"><x-i name="house" class="size-3.5" /></span>
                الصفحة الرئيسية
            </button>
        </div>
    </x-modal>
</div>
