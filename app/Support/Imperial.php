<?php

namespace App\Support;

use App\Services\AdminStore;

/**
 * Imperial Control Center registry + RBAC (mirror of src/admin/registry.ts and
 * the permission half of src/admin/store.ts). Permissions are enforced on the
 * server for every API call, and passed to the views for rendering.
 */
class Imperial
{
    public const ALL_ACTIONS = ['view', 'create', 'edit', 'delete', 'activate', 'deactivate', 'approve', 'reject', 'publish', 'archive', 'import', 'export', 'manage'];

    public static function modules(): array
    {
        return [
            ['id' => 'dashboard', 'label' => 'لوحة القيادة', 'icon' => 'layout-dashboard', 'group' => 'الرئيسية', 'kind' => 'dashboard', 'summary' => 'نظرة عامة حية'],
            ['id' => 'admin', 'label' => 'الإدارة والأمان', 'icon' => 'shield-check', 'group' => 'الإدارة والحوكمة', 'kind' => 'table', 'dataset' => 'security', 'summary' => 'سجل الأحداث الأمنية'],
            ['id' => 'rbac', 'label' => 'الأدوار والصلاحيات', 'icon' => 'user-cog', 'group' => 'الإدارة والحوكمة', 'kind' => 'rbac', 'summary' => 'مصفوفة الصلاحيات'],
            ['id' => 'approvals', 'label' => 'مركز الموافقات', 'icon' => 'clipboard-check', 'group' => 'الإدارة والحوكمة', 'kind' => 'approvals', 'dataset' => 'approvals', 'summary' => 'قرارات بانتظارك'],
            ['id' => 'audit', 'label' => 'سجل التدقيق الحي', 'icon' => 'activity-square', 'group' => 'الإدارة والحوكمة', 'kind' => 'activity', 'summary' => 'كل إجراء موثق'],

            ['id' => 'students', 'label' => 'الطلاب', 'icon' => 'graduation-cap', 'group' => 'المستخدمون', 'kind' => 'table', 'dataset' => 'students', 'summary' => 'إدارة حسابات الطلاب وحدودهم'],
            ['id' => 'student-caps', 'label' => 'صلاحيات وسقف نشاط الطلاب', 'icon' => 'user-cog', 'group' => 'المستخدمون', 'kind' => 'caps', 'dataset' => 'students', 'summary' => 'إدارة صلاحيات وسقف النشاط المصرح به'],
            ['id' => 'teachers', 'label' => 'المعلمون', 'icon' => 'users', 'group' => 'المستخدمون', 'kind' => 'table', 'dataset' => 'teachers', 'summary' => 'المعلمون المعتمدون'],
            ['id' => 'learning', 'label' => 'الملفات التعليمية', 'icon' => 'brain-circuit', 'group' => 'المستخدمون', 'kind' => 'table', 'dataset' => 'students', 'summary' => 'تقدم الطلاب ودرجاتهم'],

            ['id' => 'academic', 'label' => 'الإدارة الأكاديمية', 'icon' => 'map', 'group' => 'المحتوى الأكاديمي', 'kind' => 'table', 'dataset' => 'contents', 'summary' => 'الهيكل: صف ← مسار ← مادة ← مهارة'],
            ['id' => 'content-manager', 'label' => 'إدارة المحتوى', 'icon' => 'file-text', 'group' => 'المحتوى الأكاديمي', 'kind' => 'content-manager', 'summary' => 'تعديل أقسام صفحات المنصة بلا كود'],
            ['id' => 'content', 'label' => 'المحتوى التعليمي', 'icon' => 'book-open-check', 'group' => 'المحتوى الأكاديمي', 'kind' => 'table', 'dataset' => 'contents', 'summary' => 'فيديو · PDF · عروض · تجميعات'],
            ['id' => 'questions', 'label' => 'بنك الأسئلة', 'icon' => 'file-question', 'group' => 'المحتوى الأكاديمي', 'kind' => 'table', 'dataset' => 'questions', 'summary' => 'صياغة · مراجعة · نشر'],
            ['id' => 'exams', 'label' => 'الاختبارات والمحاكيات', 'icon' => 'clipboard-check', 'group' => 'المحتوى الأكاديمي', 'kind' => 'table', 'dataset' => 'exams', 'summary' => 'نماذج ومحاكيات قياس والتحصيلي'],
            ['id' => 'resources', 'label' => 'الملفات والتجميعات', 'icon' => 'boxes', 'group' => 'المحتوى الأكاديمي', 'kind' => 'table', 'dataset' => 'resources', 'summary' => 'مركز الغنائم التعليمية'],
            ['id' => 'media-center', 'label' => 'مركز رفع وإدارة الملفات والتجميعات', 'icon' => 'hard-drive', 'group' => 'المحتوى الأكاديمي', 'kind' => 'media', 'dataset' => 'media', 'summary' => 'PDF · فيديو · صور — رفع وإدارة'],

            ['id' => 'activities', 'label' => 'إدارة الأنشطة', 'icon' => 'activity-square', 'group' => 'التشغيل', 'kind' => 'activity', 'summary' => 'نبض المنصة كاملًا'],
            ['id' => 'stats', 'label' => 'إحصاءات المنصة', 'icon' => 'gauge', 'group' => 'المنصة', 'kind' => 'table', 'dataset' => 'stats', 'summary' => 'أرقام الرئيسية: الطلاب والرضا والأسئلة والدرجات'],
            ['id' => 'ai', 'label' => 'محرك الذكاء الاصطناعي', 'icon' => 'bot', 'group' => 'التشغيل', 'kind' => 'ai', 'summary' => 'الجلسات والاستخدام والسلامة'],
            ['id' => 'analytics', 'label' => 'التحليلات', 'icon' => 'line-chart', 'group' => 'التشغيل', 'kind' => 'analytics', 'summary' => 'نسخ العمليات والتعلم'],
            ['id' => 'seo', 'label' => 'SEO والفهرسة', 'icon' => 'globe-2', 'group' => 'التشغيل', 'kind' => 'seo', 'summary' => 'عناوين · أوصاف · Schema · Canonical · Robots'],
            ['id' => 'marketing', 'label' => 'التسويق والإعلانات', 'icon' => 'megaphone', 'group' => 'التشغيل', 'kind' => 'marketing', 'summary' => 'AdSense · Search Console · Pixels'],
            ['id' => 'health', 'label' => 'مراقبة النظام', 'icon' => 'monitor-cog', 'group' => 'التشغيل', 'kind' => 'health', 'summary' => 'صحة الخدمات الحية'],

            ['id' => 'commerce', 'label' => 'الباقات والاشتراكات', 'icon' => 'credit-card', 'group' => 'التجاري', 'kind' => 'table', 'dataset' => 'orders', 'summary' => 'طلبات · فواتير · مدفوعات'],
            ['id' => 'notifications', 'label' => 'مركز الإشعارات', 'icon' => 'bell-ring', 'group' => 'التجاري', 'kind' => 'table', 'dataset' => 'notifications', 'summary' => 'حملات فورية ومجدولة'],
            ['id' => 'support', 'label' => 'مركز الدعم', 'icon' => 'messages-square', 'group' => 'التجاري', 'kind' => 'table', 'dataset' => 'notifications', 'summary' => 'تذاكر ومحادثات (عرض موحد)'],

            ['id' => 'cms', 'label' => 'إدارة المنصة CMS', 'icon' => 'megaphone', 'group' => 'المنصة', 'kind' => 'cms', 'summary' => 'المحتوى التسويقي — تعديل بلا كود'],
            ['id' => 'testimonials', 'label' => 'قصص النجاح', 'icon' => 'trophy', 'group' => 'المنصة', 'kind' => 'table', 'dataset' => 'testimonials', 'summary' => 'إدارة شهادات ودرجات الطلاب'],
            ['id' => 'flags', 'label' => 'أعلام المزايا', 'icon' => 'flag', 'group' => 'المنصة', 'kind' => 'table', 'dataset' => 'flags', 'summary' => 'تفعيل/تعطيل لكل شريحة'],
            ['id' => 'settings', 'label' => 'إعدادات المنصة', 'icon' => 'settings-2', 'group' => 'المنصة', 'kind' => 'settings', 'summary' => 'التهيئة المركزية'],
        ];
    }

    public static function module(string $id): ?array
    {
        foreach (self::modules() as $m) {
            if ($m['id'] === $id) {
                return $m;
            }
        }

        return null;
    }

    public static function columns(string $dataset): array
    {
        $c = fn (string $key, string $label, bool $badge = false) => compact('key', 'label', 'badge');

        return match ($dataset) {
            'students' => [$c('name', 'الطالب'), $c('grade', 'المرحلة'), $c('phone', 'الجوال'), $c('plan', 'الباقة', true), $c('simsUsed', 'المحاكي المستخدم'), $c('simsLimit', 'السقف'), $c('status', 'الحالة', true)],
            'teachers' => [$c('name', 'المعلم'), $c('subject', 'التخصص'), $c('students', 'الطلاب'), $c('groups', 'المجموعات'), $c('status', 'الحالة', true)],
            'questions' => [$c('id', 'المعرف'), $c('skill', 'المهارة'), $c('level', 'الصعوبة', true), $c('status', 'الحالة', true), $c('author', 'المُنشئ'), $c('version', 'الإصدار')],
            'exams' => [$c('title', 'النموذج'), $c('questions', 'الأسئلة'), $c('duration', 'المدة'), $c('attempts', 'المحاولات'), $c('status', 'الحالة', true)],
            'contents' => [$c('title', 'المحتوى'), $c('kind', 'النوع', true), $c('status', 'الحالة', true), $c('owner', 'المسؤول'), $c('updated', 'آخر تحديث')],
            'resources' => [$c('name', 'الملف'), $c('type', 'النوع', true), $c('size', 'الحجم'), $c('downloads', 'التحميلات'), $c('visibility', 'الوصول', true)],
            'orders' => [$c('id', 'الطلب'), $c('customer', 'العميل'), $c('plan', 'الباقة'), $c('amount', 'المبلغ'), $c('status', 'الحالة', true)],
            'notifications' => [$c('title', 'الإشعار'), $c('audience', 'الجمهور', true), $c('channel', 'القناة'), $c('status', 'الحالة', true)],
            'security' => [$c('type', 'الحدث'), $c('actor', 'الفاعل'), $c('severity', 'الخطورة', true), $c('status', 'الحالة', true), $c('at', 'الوقت')],
            'flags' => [$c('feature', 'الميزة'), $c('target', 'الجمهور'), $c('enabled', 'الحالة', true), $c('owner', 'المالك')],
            'stats' => [$c('label', 'الإحصائية'), $c('value', 'القيمة'), $c('suffix', 'اللاحقة'), $c('decimals', 'الكسور'), $c('iconKey', 'الأيقونة: crown·trophy·book·target·users', true), $c('enabled', 'الظهور في الرئيسية', true)],
            'testimonials' => [$c('name', 'الطالب'), $c('score', 'الدرجة'), $c('exam', 'الاختبار', true), $c('text', 'الشهادة'), $c('city', 'المدينة', true)],
            'audit' => [$c('at', 'الوقت'), $c('actor', 'الفاعل'), $c('action', 'الإجراء'), $c('module', 'الوحدة', true), $c('detail', 'التفاصيل'), $c('result', 'النتيجة', true)],
            default => [],
        };
    }

    /* =============================== RBAC =============================== */

    private const SUPER_MODULES = [
        'dashboard', 'admin', 'students', 'teachers', 'academic', 'content', 'questions', 'exams',
        'activities', 'resources', 'learning', 'ai', 'analytics', 'commerce', 'notifications',
        'cms', 'support', 'security', 'rbac', 'approvals', 'flags', 'stats', 'student-caps', 'media-center', 'crm', 'settings', 'health', 'testimonials', 'content-manager', 'seo', 'marketing',
    ];

    private const TEACHER_MATRIX = [
        'dashboard' => ['view' => true, 'actions' => ['view']],
        'students' => ['view' => true, 'actions' => ['view', 'export'], 'scope' => 'ASSIGNED — طلاب مجموعاته فقط'],
        'student-caps' => ['view' => true, 'actions' => ['view'], 'scope' => 'ASSIGNED'],
        'media-center' => ['view' => true, 'actions' => ['view'], 'scope' => 'ASSIGNED'],
        'questions' => ['view' => true, 'actions' => ['view', 'create', 'edit', 'import', 'export'], 'scope' => 'OWNED — أسئلته فقط'],
        'content' => ['view' => true, 'actions' => ['view', 'create', 'edit'], 'scope' => 'OWNED'],
        'exams' => ['view' => true, 'actions' => ['view', 'create', 'edit'], 'scope' => 'ASSIGNED'],
        'approvals' => ['view' => true, 'actions' => ['view']],
        'activities' => ['view' => true, 'actions' => ['view']],
        'analytics' => ['view' => true, 'actions' => ['view', 'export']],
        'resources' => ['view' => true, 'actions' => ['view', 'import', 'export']],
        'learning' => ['view' => true, 'actions' => ['view']],
    ];

    private const ACADEMIC_BASE = ['dashboard', 'content', 'questions', 'exams', 'academic', 'analytics', 'approvals', 'ai', 'activities'];
    private const SUPPORT_BASE = ['dashboard', 'support', 'students', 'notifications'];

    public static function roleDefs(): array
    {
        return [
            ['id' => 'SUPER_ADMIN', 'name' => 'الإدارة العليا', 'tone' => 'danger', 'desc' => 'GLOBAL — كل الصلاحيات'],
            ['id' => 'ACADEMIC_MGR', 'name' => 'المشرف الأكاديمي', 'tone' => 'primary', 'desc' => 'ACADEMIC — نطاق المحتوى الأكاديمي'],
            ['id' => 'TEACHER', 'name' => 'المعلم المعتمد', 'tone' => 'secondary', 'desc' => 'OWNED/ASSIGNED — طلابه ومحتواه فقط'],
            ['id' => 'SUPPORT', 'name' => 'وكيل الدعم', 'tone' => 'neutral', 'desc' => 'READ-ONLY + تذاكر فقط'],
        ];
    }

    private static function perm(string $role, string $module): ?array
    {
        if ($role === 'SUPER_ADMIN') {
            return in_array($module, self::SUPER_MODULES, true) ? ['view' => true, 'actions' => self::ALL_ACTIONS] : null;
        }

        return self::TEACHER_MATRIX[$module] ?? null;
    }

    public static function baseAllowed(string $roleId, string $module): bool
    {
        return match ($roleId) {
            'SUPER_ADMIN' => true,
            'ACADEMIC_MGR' => in_array($module, self::ACADEMIC_BASE, true),
            'SUPPORT' => in_array($module, self::SUPPORT_BASE, true),
            'TEACHER' => (bool) (self::TEACHER_MATRIX[$module]['view'] ?? false),
            default => false,
        };
    }

    public static function overrides(): array
    {
        return app(AdminStore::class)->setting('rbac', []);
    }

    public static function effectiveAllowed(string $roleId, string $module, ?array $overrides = null): bool
    {
        $o = ($overrides ?? self::overrides())[$roleId][$module] ?? null;

        return $o === null ? self::baseAllowed($roleId, $module) : (bool) $o;
    }

    /** can(role, module, action) — live RBAC overrides apply to the TEACHER role */
    public static function can(string $role, string $module, string $action, ?array $overrides = null): bool
    {
        if ($role === 'TEACHER') {
            $o = ($overrides ?? self::overrides())['TEACHER'][$module] ?? null;
            if ($o !== null) {
                return (bool) $o;
            }
        }
        $m = self::perm($role, $module);

        return (bool) ($m['view'] ?? false) && ($action === 'view' || in_array($action, $m['actions'], true));
    }

    public static function scope(string $role, string $module): ?string
    {
        return self::perm($role, $module)['scope'] ?? null;
    }

    /** every action flag for one module, for the views */
    public static function permsFor(string $role, string $module, ?array $overrides = null): array
    {
        $overrides ??= self::overrides();
        $out = [];
        foreach (self::ALL_ACTIONS as $a) {
            $out[$a] = self::can($role, $module, $a, $overrides);
        }

        return $out;
    }

    /** modules this role can see, in sidebar order */
    public static function visibleModules(string $role): array
    {
        $ov = self::overrides();

        return array_values(array_filter(self::modules(), fn ($m) => self::can($role, $m['id'], 'view', $ov)));
    }

    /**
     * Which datasets a module is allowed to write to — prevents a role from
     * using a permitted module to touch an unrelated dataset.
     */
    public static function datasetsFor(string $module): array
    {
        return match ($module) {
            'content-manager' => ['contents', 'questions', 'resources', 'media', 'testimonials'],
            'activities', 'audit' => ['audit'],
            'analytics' => ['audit'],
            default => array_filter([self::module($module)['dataset'] ?? null]),
        };
    }
}
