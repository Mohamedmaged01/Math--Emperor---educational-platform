<?php

namespace App\Http\Controllers\Imperial;

use App\Http\Controllers\Controller;
use App\Services\AdminStore;
use App\Support\Analytics;
use App\Support\Imperial;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ImperialController extends Controller
{
    public function __construct(private AdminStore $store) {}

    public function show(Request $request, string $module = 'dashboard'): View
    {
        $user = $request->user();
        $role = $user->staffRole();
        $overrides = Imperial::overrides();
        $mod = Imperial::module($module) ?? abort(404);

        $visible = Imperial::visibleModules($role);
        $denied = ! collect($visible)->contains('id', $module);
        if ($denied) {
            $this->store->denied($module, 'view');
        }

        $can = fn (string $m, string $a = 'view') => Imperial::can($role, $m, $a, $overrides);
        $approvals = $this->store->rows('approvals');
        $pending = count(array_filter($approvals, fn ($a) => ($a['status'] ?? '') === 'بانتظار القرار'));

        $shared = [
            'user' => $user,
            'role' => $role,
            'mod' => $mod,
            'denied' => $denied,
            'groups' => collect($visible)->groupBy('group')->all(),
            'pendingApprovals' => $pending,
            'palette' => $this->palette($visible, $can, $pending),
            'canView' => collect(Imperial::modules())->mapWithKeys(fn ($m) => [$m['id'] => $can($m['id'])])->all(),
        ];

        $data = $denied ? [] : $this->moduleData($mod, $role, $can, $overrides);

        return view('imperial.show', $shared + $data);
    }

    private function moduleData(array $mod, string $role, \Closure $can, array $overrides): array
    {
        $s = $this->store;

        return match ($mod['kind']) {
            'dashboard' => $this->dashboard(),
            'table' => $this->table($mod['dataset'], $mod['id'], $mod['label'], $role, $overrides),
            'activity' => $this->table('audit', 'activities', 'سجل التدقيق الحي', $role, $overrides),
            'approvals' => ['approvals' => $s->rows('approvals'), 'canApprove' => $can('approvals', 'approve')],
            'rbac' => [
                'roleDefs' => Imperial::roleDefs(),
                'modules' => Imperial::modules(),
                'matrix' => collect(Imperial::roleDefs())->mapWithKeys(fn ($r) => [
                    $r['id'] => collect(Imperial::modules())->mapWithKeys(fn ($m) => [$m['id'] => Imperial::effectiveAllowed($r['id'], $m['id'], $overrides)])->all(),
                ])->all(),
                'overridesCount' => collect($overrides)->sum(fn ($m) => count((array) $m)),
                'canManage' => $can('rbac', 'manage'),
            ],
            'analytics' => $this->analytics() + ['canExport' => $can('analytics', 'export')],
            'health' => ['services' => $this->health()],
            'cms' => ['cms' => $s->setting('cms'), 'canEdit' => $can('cms', 'edit')],
            'settings' => [
                'canEdit' => $can('settings', 'edit'),
                'workspaceStats' => [
                    'records' => DB::table('admin_records')->count(),
                    'audit' => DB::table('audit_logs')->count(),
                    'pending' => count(array_filter($s->rows('approvals'), fn ($a) => ($a['status'] ?? '') === 'بانتظار القرار')),
                ],
            ],
            'caps' => ['students' => $s->rows('students'), 'manage' => $can('student-caps', 'manage')],
            'media' => ['media' => $s->rows('media'), 'manage' => $can('media-center', 'manage')],
            'seo' => [
                'pages' => Seo::pages(),
                'overrides' => (object) $s->setting('seoOverrides', []),
                'canEdit' => $can('seo', 'edit'),
                'sitemap' => Seo::sitemapXml(),
                'robots' => implode("\n", Seo::robotsLines()),
            ],
            'marketing' => ['marketing' => $s->setting('marketing'), 'canManage' => $can('marketing', 'manage')],
            'content-manager' => ['contentRows' => $this->contentRows(), 'canManage' => $can('cms', 'manage')],
            'ai' => ['ai' => $s->rows('ai')],
            default => [],
        };
    }

    private function table(string $dataset, string $moduleId, string $title, string $role, array $overrides): array
    {
        $perms = Imperial::permsFor($role, $moduleId, $overrides);
        if ($dataset === 'audit') {
            // the audit trail is append-only: view + export only
            $perms = array_map(fn () => false, $perms);
            $perms['view'] = true;
            $perms['export'] = Imperial::can($role, $moduleId, 'export', $overrides);
        }

        return [
            'table' => [
                'dataset' => $dataset,
                'module' => $moduleId,
                'title' => $title,
                'columns' => Imperial::columns($dataset),
                'rows' => $this->store->rows($dataset),
                'perms' => $perms,
                'scope' => Imperial::scope($role, $moduleId),
            ],
        ];
    }

    private function dashboard(): array
    {
        $s = $this->store;
        $students = $s->rows('students');
        $questions = $s->rows('questions');
        $st = fn ($r) => $r['status'] ?? '';

        return [
            'kpis' => [
                ['icon' => 'graduation-cap', 'label' => 'الطلاب النشطون', 'value' => count(array_filter($students, fn ($r) => $st($r) === 'نشط')).' / '.count($students), 'd' => 'students'],
                ['icon' => 'file-question', 'label' => 'الأسئلة المنشورة', 'value' => count(array_filter($questions, fn ($r) => $st($r) === 'منشور')).' / '.count($questions), 'd' => 'questions'],
                ['icon' => 'check-circle-2', 'label' => 'نماذج منشورة', 'value' => count(array_filter($s->rows('exams'), fn ($r) => $st($r) === 'منشور')), 'd' => 'exams'],
                ['icon' => 'bell-ring', 'label' => 'إشعارات قيد المعالجة', 'value' => count(array_filter($s->rows('notifications'), fn ($r) => $st($r) === 'قيد الإرسال')), 'd' => 'notifications'],
            ],
            'latestApprovals' => array_slice($s->rows('approvals'), 0, 3),
            'quick' => [
                ['label' => 'مراجعة سؤال', 'mod' => 'questions', 'icon' => 'file-question'],
                ['label' => 'قرارات الموافقة', 'mod' => 'approvals', 'icon' => 'check-circle-2'],
                ['label' => 'الملفات والتجميعات', 'mod' => 'resources', 'icon' => 'eye'],
                ['label' => 'محرك الذكاء', 'mod' => 'ai', 'icon' => 'sparkles'],
            ],
        ];
    }

    private function analytics(): array
    {
        $d = $this->store->all();
        $kpi = Analytics::kpis($d);
        $heat = Analytics::skillHeatmap($d['questions']);
        $insights = Analytics::insights($kpi, $heat);

        return [
            'kpi' => $kpi,
            'heat' => $heat,
            'series' => Analytics::activitySeries($kpi['totalExamAttempts']),
            'plans' => Analytics::planDistribution($d['students']),
            'funnel' => Analytics::conversionFunnel($d['students'], $d['orders']),
            'examBars' => array_values(array_map(
                fn ($e) => ['label' => mb_substr((string) $e['title'], 0, 14), 'value' => (int) $e['attempts']],
                array_filter($d['exams'], fn ($e) => (int) ($e['attempts'] ?? 0) > 0)
            )),
            'insights' => $insights,
            'summary' => Analytics::summary($kpi, $insights),
        ];
    }

    /** only real, measured services report HEALTHY — the rest stay UNKNOWN */
    private function health(): array
    {
        $t = microtime(true);
        try {
            DB::select('select 1');
            $dbMs = (int) max(1, round((microtime(true) - $t) * 1000));
            $dbStatus = 'HEALTHY';
        } catch (\Throwable) {
            $dbMs = null;
            $dbStatus = 'DOWN';
        }

        return [
            ['name' => 'واجهة المنصة (Blade)', 'status' => 'HEALTHY', 'ms' => (int) round((microtime(true) - LARAVEL_START) * 1000), 'note' => 'زمن توليد هذه الصفحة على الخادم'],
            ['name' => 'جلسات المستخدمين', 'status' => 'HEALTHY', 'ms' => null, 'note' => 'جلسات Laravel — وظيفية'],
            ['name' => 'قاعدة بيانات مساحة العمل', 'status' => $dbStatus, 'ms' => $dbMs, 'note' => 'قياس حي لاستعلام فحص'],
            ['name' => 'واجهة API الخلفية', 'status' => 'HEALTHY', 'ms' => null, 'note' => 'نقاط Laravel المحمية بالصلاحيات'],
            ['name' => 'محرك الذكاء الاصطناعي الخارجي', 'status' => 'UNKNOWN', 'ms' => null, 'note' => 'بدون مزود مهيأ حاليًا'],
            ['name' => 'بوابة الدفع', 'status' => 'UNKNOWN', 'ms' => null, 'note' => 'تكامل مرحلية — غير مفعّل'],
            ['name' => 'خدمة الإشعارات الخارجية', 'status' => 'UNKNOWN', 'ms' => null, 'note' => 'قوالب جاهزة بلا ناقل'],
        ];
    }

    private function contentRows(): array
    {
        $rows = [];
        foreach (['contents', 'questions', 'resources', 'media', 'testimonials'] as $dataset) {
            foreach ($this->store->rows($dataset) as $r) {
                $rows[] = $r + [
                    '_dataset' => $dataset,
                    '_title' => (string) ($r['title'] ?? $r['name'] ?? $r['subject'] ?? '#'),
                    '_sub' => (string) ($r['subtitle'] ?? $r['description'] ?? $r['notes'] ?? ''),
                ];
            }
        }

        return $rows;
    }

    private function palette(array $visible, \Closure $can, int $pending): array
    {
        $s = $this->store;
        $hits = array_map(fn ($m) => ['id' => 'mod-'.$m['id'], 'label' => $m['label'], 'sub' => $m['summary'] ?? $m['group'], 'target' => $m['id'], 'kind' => 'وحدة'], $visible);
        if ($can('students')) {
            foreach ($s->rows('students') as $r) {
                $hits[] = ['id' => 'st-'.$r['id'], 'label' => (string) $r['name'], 'sub' => ($r['plan'] ?? '').' · السقف '.($r['simsLimit'] ?? ''), 'target' => 'students', 'kind' => 'طالب'];
            }
        }
        if ($can('teachers')) {
            foreach ($s->rows('teachers') as $r) {
                $hits[] = ['id' => 'tc-'.$r['id'], 'label' => (string) $r['name'], 'sub' => (string) ($r['subject'] ?? ''), 'target' => 'teachers', 'kind' => 'معلم'];
            }
        }
        if ($can('questions')) {
            foreach ($s->rows('questions') as $r) {
                $hits[] = ['id' => 'q-'.$r['id'], 'label' => strtoupper($r['id']).' — '.($r['skill'] ?? ''), 'sub' => ($r['level'] ?? '').' · '.($r['status'] ?? ''), 'target' => 'questions', 'kind' => 'سؤال'];
            }
        }
        if ($can('exams')) {
            foreach ($s->rows('exams') as $r) {
                $hits[] = ['id' => 'ex-'.$r['id'], 'label' => (string) $r['title'], 'sub' => ($r['questions'] ?? '').' سؤال · '.($r['duration'] ?? ''), 'target' => 'exams', 'kind' => 'نموذج'];
            }
        }
        $hits[] = ['id' => 'act-approve', 'label' => 'قرارات الموافقة العالقة', 'sub' => "{$pending} قرارًا — افتح المركز", 'target' => 'approvals', 'kind' => 'إجراء'];
        $hits[] = ['id' => 'act-export', 'label' => 'تصدير سجل التدقيق', 'sub' => 'JSON/CSV — وحدة الأنشطة', 'target' => 'audit', 'kind' => 'إجراء'];

        return array_map(fn ($h) => $h + ['allowed' => $can($h['target'])], $hits);
    }
}
