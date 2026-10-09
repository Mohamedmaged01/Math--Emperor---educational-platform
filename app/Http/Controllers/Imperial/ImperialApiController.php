<?php

namespace App\Http\Controllers\Imperial;

use App\Http\Controllers\Controller;
use App\Services\AdminStore;
use App\Support\Imperial;
use App\Support\Seo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * JSON endpoints behind every action in the Imperial Control Center.
 * Each call is permission-checked against the RBAC matrix; refused calls are
 * written to the audit trail as "منع صلاحية" (exactly like the UI did).
 */
class ImperialApiController extends Controller
{
    private const MEDIA_CATEGORIES = ['شروحات مرئية', 'تجميعات 120', 'حقائب تدريبية', 'صور أسئلة'];

    public function __construct(private AdminStore $store) {}

    /* ============================ generic records ============================ */

    public function store(Request $request, string $dataset): JsonResponse
    {
        $module = $this->moduleFor($request, $dataset, 'create');
        $row = $this->cleanRow($request->input('row', []));
        $created = $this->store->insert($dataset, $module, $row);
        $this->maybeNote($request, $module);

        return $this->rows($dataset, ['row' => $created]);
    }

    public function update(Request $request, string $dataset, string $key): JsonResponse
    {
        $module = $this->moduleFor($request, $dataset, 'edit');
        $this->store->update($dataset, $module, $key, $this->cleanRow($request->input('patch', [])))
            ?? abort(404);
        $this->maybeNote($request, $module);

        return $this->rows($dataset);
    }

    public function transition(Request $request, string $dataset, string $key): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:publish,archive'],
            'label' => ['nullable', Rule::in(['نشر', 'أرشفة', 'أرشفة بدل الحذف'])],
        ]);
        $module = $this->moduleFor($request, $dataset, $data['action']);
        $status = $data['action'] === 'publish' ? 'منشور' : 'مؤرشف';
        $this->store->transition($dataset, $module, $key, $data['label'] ?? ($data['action'] === 'publish' ? 'نشر' : 'أرشفة'), $status);

        return $this->rows($dataset);
    }

    public function destroy(Request $request, string $dataset, string $key): JsonResponse
    {
        $module = $this->moduleFor($request, $dataset, 'delete');
        $reason = trim((string) $request->input('reason', ''));
        if ($reason === '') {
            return response()->json(['error' => 'سبب الحذف إلزامي.'], 422);
        }
        if ($dataset === 'media') {
            $this->deleteMediaFile($key);
        }
        $this->store->remove($dataset, $module, $key, $reason);
        $this->maybeNote($request, $module);

        return $this->rows($dataset);
    }

    public function bulk(Request $request, string $dataset): JsonResponse
    {
        $data = $request->validate([
            'keys' => ['required', 'array', 'max:500'],
            'keys.*' => ['string', 'max:64'],
            'kind' => ['required', 'in:archive,publish'],
        ]);
        $module = $this->moduleFor($request, $dataset, 'archive');
        $this->store->bulk($dataset, $module, $data['keys'],
            $data['kind'] === 'archive' ? 'أرشفة' : 'نشر',
            $data['kind'] === 'archive' ? 'مؤرشف' : 'منشور');

        return $this->rows($dataset);
    }

    public function import(Request $request, string $dataset): JsonResponse
    {
        $request->validate(['rows' => ['required', 'array', 'min:1', 'max:1000']]);
        $module = $this->moduleFor($request, $dataset, 'import');
        $rows = array_map(fn ($r) => $this->cleanRow(is_array($r) ? $r : []), $request->input('rows'));
        $this->store->import($dataset, $module, $rows);

        return $this->rows($dataset);
    }

    public function export(Request $request, string $dataset): StreamedResponse
    {
        $request->validate(['format' => ['required', 'in:csv,json']]);
        $module = $this->moduleFor($request, $dataset, 'export');
        $ids = array_filter(explode(',', (string) $request->query('ids', '')));
        $rows = array_values(array_filter($this->store->rows($dataset), fn ($r) => ! $ids || in_array((string) $r['id'], $ids, true)));
        $format = $request->query('format');
        $stamp = $dataset.'-'.now()->getTimestampMs();

        $this->store->note('تصدير', $module, count($rows).' سجل — '.strtoupper($format).($ids ? ' (محدد)' : ''));

        if ($format === 'json') {
            return response()->streamDownload(
                fn () => print(json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
                "{$stamp}.json", ['Content-Type' => 'application/json']
            );
        }

        return response()->streamDownload(function () use ($rows) {
            $cols = array_keys($rows[0] ?? ['id' => '']);
            $esc = fn ($v) => '"'.str_replace('"', '""', is_bool($v) ? ($v ? 'true' : 'false') : (string) $v).'"';
            echo "\u{FEFF}".implode(',', $cols);
            foreach ($rows as $r) {
                echo "\n".implode(',', array_map(fn ($c) => $esc($r[$c] ?? ''), $cols));
            }
        }, "{$stamp}.csv", ['Content-Type' => 'text/csv; charset=utf-8']);
    }

    /* ================================ approvals ================================ */

    public function decide(Request $request, string $key): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:approve,reject'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $this->guard('approvals', 'approve');
        if ($data['type'] === 'reject' && trim((string) ($data['note'] ?? '')) === '') {
            return response()->json(['error' => 'سبب الرفض إلزامي.'], 422);
        }
        $approve = $data['type'] === 'approve';
        $this->store->transition('approvals', 'approvals', $key, $approve ? 'اعتماد بالموافقة' : 'رفض قرار', $approve ? 'معتمد ✅' : 'مرفوض ⛔');
        $this->store->note($approve ? 'قرار اعتماد' : 'قرار رفض', 'approvals', ($data['note'] ?? '') ?: 'بدون سبب إضافي');

        return $this->rows('approvals');
    }

    /* =================================== rbac =================================== */

    public function rbacToggle(Request $request): JsonResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(array_column(Imperial::roleDefs(), 'id'))],
            'module' => ['required', Rule::in(array_column(Imperial::modules(), 'id'))],
        ]);
        $this->guard('rbac', 'manage');
        if ($data['role'] === 'SUPER_ADMIN') {
            return response()->json(['allowed' => true]); // platform root can never be reduced
        }
        $rbac = $this->store->setting('rbac', []);
        $next = ! Imperial::effectiveAllowed($data['role'], $data['module'], $rbac);
        $rbac[$data['role']][$data['module']] = $next;
        $this->store->putSetting('rbac', $rbac);
        $this->store->audit('تغيير صلاحية', 'rbac', "{$data['role']} / {$data['module']}", 'القفل ← '.($next ? 'مفتوح (مسموح)' : 'مقفل (ممنوع)'));

        return response()->json(['allowed' => $next, 'overridesCount' => collect($rbac)->sum(fn ($m) => count((array) $m))]);
    }

    public function rbacReset(): JsonResponse
    {
        $this->guard('rbac', 'manage');
        $this->store->putSetting('rbac', new \stdClass());
        $this->store->audit('إعادة تعيين صلاحيات', 'rbac', 'matrix', 'إرجاع كل الأقفال إلى الوضع الافتراضي المعتمد');

        return response()->json(['ok' => true]);
    }

    /* ======================== cms · marketing · seo · settings ======================== */

    public function cms(Request $request): JsonResponse
    {
        $this->guard('cms', 'edit');
        $data = $request->validate([
            'heroLine1' => ['required', 'string', 'max:120'],
            'heroLine2Gold' => ['required', 'string', 'max:120'],
            'heroLine3' => ['required', 'string', 'max:120'],
        ]);
        $this->store->putSetting('cms', array_merge($this->store->setting('cms'), $data, ['published' => true]));
        $this->store->note('نشر محتوى CMS', 'cms', 'تحديث عنوان قسم البطل — منشور للواجهة');

        return response()->json(['ok' => true, 'cms' => $this->store->setting('cms')]);
    }

    public function marketing(Request $request): JsonResponse
    {
        $this->guard('marketing', 'manage');
        $patch = $request->validate([
            'publisherId' => ['sometimes', 'nullable', 'string', 'max:40'],
            'adsenseEnabled' => ['sometimes', 'boolean'],
            'autoAds' => ['sometimes', 'boolean'],
            'testMode' => ['sometimes', 'boolean'],
            'maxAdsPerPage' => ['sometimes', 'integer', 'between:0,3'],
            'placements' => ['sometimes', 'array'],
            'placements.*' => ['boolean'],
        ]);
        if (array_key_exists('publisherId', $patch)) {
            $patch['publisherId'] = trim((string) $patch['publisherId']);
            if ($patch['publisherId'] !== '' && ! Seo::isValidPublisherId($patch['publisherId'])) {
                return response()->json(['error' => 'صيغة ID ناشر غير صحيحة — يجب أن تكون «ca-pub- ثم 12–18 رقمًا». لا تُحفظ قيم غير صالحة.'], 422);
            }
        }
        $m = array_merge($this->store->setting('marketing'), $patch);
        $this->store->putSetting('marketing', $m);
        $this->store->note('تحديث إعدادات التسويق والإعلانات', 'marketing', mb_substr(json_encode($patch, JSON_UNESCAPED_UNICODE), 0, 120));

        return response()->json(['ok' => true, 'marketing' => $m]);
    }

    public function seo(Request $request): JsonResponse
    {
        $this->guard('seo', 'edit');
        $data = $request->validate([
            'page' => ['required', Rule::in(array_keys(Seo::pages()))],
            'title' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:400'],
            'primaryKeyword' => ['nullable', 'string', 'max:120'],
            'secondaryKeywords' => ['nullable', 'string', 'max:400'],
            'robots' => ['nullable', Rule::in(['index,follow', 'index,nofollow', 'noindex,nofollow'])],
        ]);
        $clean = array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'primaryKeyword' => $data['primaryKeyword'] ?? null,
            'secondaryKeywords' => ! empty($data['secondaryKeywords'])
                ? array_values(array_filter(array_map('trim', preg_split('/[,،]/u', $data['secondaryKeywords'])))) : null,
            'robots' => $data['robots'] ?? null,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);

        $page = $data['page'];
        if (! $clean) {
            $this->store->note('SEO تعديل مُلغى — لا تغييرات', 'seo', 'لا تغييرات وقعت');

            return response()->json(['ok' => true]);
        }
        $all = $this->store->setting('seoOverrides', []);
        $all[$page] = array_merge($all[$page] ?? [], $clean);
        $this->store->putSetting('seoOverrides', $all);
        $this->store->note('SEO تحديث', 'seo', 'الصفحة «'.Seo::pages()[$page]['route'].'» — تعديلات محفوظة: '.implode(',', array_keys($clean)));

        return response()->json(['ok' => true, 'overrides' => $all]);
    }

    public function settings(Request $request): JsonResponse
    {
        $this->guard('settings', 'edit');
        $cfg = $request->validate(['cfg' => ['required', 'array'], 'cfg.*' => ['nullable', 'string', 'max:120']])['cfg'];
        $this->store->note('تحديث إعدادات', 'settings', collect($cfg)->map(fn ($v, $k) => "{$k}={$v}")->implode(' · '));

        return response()->json(['ok' => true]);
    }

    public function resetWorkspace(): JsonResponse
    {
        $this->guard('settings', 'manage');
        $this->store->resetAll('إعادة تعيين يدوية من قائمة الإعدادات الجانبية');

        return response()->json(['ok' => true]);
    }

    /* =================================== media =================================== */

    public function mediaStore(Request $request): JsonResponse
    {
        $this->guard('media-center', 'manage');
        $data = $this->validateMedia($request);
        $data['kind'] = $this->kindFromUrl($data['url'] ?? '') ?? 'file';
        $data['size'] = $data['size'] ?? '—';

        if ($file = $request->file('file')) {
            $path = $file->store('media', 'public');
            $data['url'] = Storage::disk('public')->url($path);
            $data['path'] = $path;
            $data['size'] = $this->sizeLabel($file->getSize());
            $data['kind'] = str_starts_with((string) $file->getMimeType(), 'video/') ? 'video-mp4' : 'file';
        }

        $this->store->insert('media', 'media-center', $data + [
            'date' => now()->toDateString(), 'downloads' => 0, 'notes' => '',
        ]);
        $this->store->note('رفع/إضافة ملف جديد', 'media-center', $data['title']);

        return $this->rows('media');
    }

    public function mediaUpdate(Request $request, string $key): JsonResponse
    {
        $this->guard('media-center', 'manage');
        $data = $this->validateMedia($request);
        unset($data['uploader']);
        $this->store->update('media', 'media-center', $key, array_filter($data, fn ($v) => $v !== null)) ?? abort(404);
        $this->store->note('تعديل ملف/فيديو', 'media-center', $data['title']);

        return $this->rows('media');
    }

    /* ============================ audit helpers (client) ============================ */

    public function note(Request $request): JsonResponse
    {
        $data = $request->validate([
            'module' => ['required', Rule::in(array_column(Imperial::modules(), 'id'))],
            'action' => ['required', 'string', 'max:80'],
            'detail' => ['required', 'string', 'max:500'],
            'result' => ['nullable', 'in:success,denied,failed'],
        ]);
        $this->guard($data['module'], 'view');
        $this->store->note($data['action'], $data['module'], $data['detail'], $data['result'] ?? 'success');

        return response()->json(['ok' => true]);
    }

    public function denied(Request $request): JsonResponse
    {
        $data = $request->validate([
            'module' => ['required', 'string', 'max:48'],
            'action' => ['required', 'string', 'max:24'],
        ]);
        $this->store->denied($data['module'], $data['action']);

        return response()->json(['ok' => true]);
    }

    /* ================================== helpers ================================== */

    /** resolve + authorize the module performing an operation on a dataset */
    private function moduleFor(Request $request, string $dataset, string $op): string
    {
        $module = (string) $request->input('module', $request->query('module', ''));
        if (! Imperial::module($module) || ! in_array($dataset, Imperial::datasetsFor($module), true)) {
            abort(response()->json(['error' => 'وحدة أو مجموعة بيانات غير صالحة.'], 422));
        }
        if ($dataset === 'audit' && $op !== 'export') {
            abort(response()->json(['error' => 'سجل التدقيق للقراءة فقط.'], 403));
        }

        [$permModule, $action] = match ($module) {
            'content-manager' => ['cms', 'manage'],
            'student-caps' => ['student-caps', 'manage'],
            'media-center' => ['media-center', 'manage'],
            default => [$module, $op],
        };
        $this->guard($permModule, $action);

        return $module === 'content-manager' ? 'cms' : $module;
    }

    private function guard(string $module, string $action): void
    {
        $role = auth()->user()->staffRole();
        if (! Imperial::can($role, $module, $action)) {
            $this->store->denied($module, $action);
            abort(response()->json(['error' => 'صلاحياتك لا تخوّل هذا الإجراء — تم تسجيل المحاولة.', 'denied' => true], 403));
        }
    }

    private function rows(string $dataset, array $extra = []): JsonResponse
    {
        return response()->json(['rows' => $this->store->rows($dataset)] + $extra);
    }

    /** only flat scalar values may enter a dataset row */
    private function cleanRow(mixed $row): array
    {
        if (! is_array($row)) {
            return [];
        }
        $out = [];
        foreach ($row as $k => $v) {
            if (! is_string($k) || $k === '' || mb_strlen($k) > 40 || str_starts_with($k, '_')) {
                continue;
            }
            if (is_bool($v) || is_int($v) || is_float($v)) {
                $out[$k] = $v;
            } elseif (is_string($v)) {
                $out[$k] = mb_substr($v, 0, 2000);
            } elseif ($v === null) {
                $out[$k] = '';
            }
        }

        return $out;
    }

    private function maybeNote(Request $request, string $module): void
    {
        $n = $request->input('note');
        if (is_array($n) && ! empty($n['action']) && ! empty($n['detail'])) {
            $this->store->note(mb_substr((string) $n['action'], 0, 80), $module, mb_substr((string) $n['detail'], 0, 500));
        }
    }

    private function validateMedia(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', Rule::in(self::MEDIA_CATEGORIES)],
            'uploader' => ['nullable', 'string', 'max:120'],
            'duration' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:2000'],
            'url' => ['nullable', 'url', 'max:500'],
            'file' => ['nullable', 'file', 'max:204800', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,png,jpg,jpeg,mp4,webm,mov'],
        ]) + ['uploader' => auth()->user()->name];
    }

    private function kindFromUrl(string $url): ?string
    {
        $u = strtolower($url);

        return match (true) {
            $u === '' => null,
            str_contains($u, 'youtube.com') || str_contains($u, 'youtu.be') => 'video-yt',
            str_contains($u, 'drive.google.') || str_contains($u, 'docs.google.') => 'video-drive',
            default => null,
        };
    }

    private function deleteMediaFile(string $key): void
    {
        $path = $this->store->find('media', $key)?->data['path'] ?? null;
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function sizeLabel(int $bytes): string
    {
        if ($bytes < 1024) {
            return "{$bytes} بايت";
        }
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1).' كيلوبايت';
        }

        return number_format($bytes / 1024 / 1024, 1).' ميجابايت';
    }
}
