<?php

namespace App\Support;

use App\Services\AdminStore;

/** SEO registry + JSON-LD + sitemap/robots (mirror of src/seo/engine.ts). */
class Seo
{
    public const DOMAIN = 'mathemperor.sa';

    public static function pages(): array
    {
        $d = self::DOMAIN;

        return [
            'home' => [
                'route' => '/', 'title' => 'منصة إمبراطور الرياضيات | بوابتك للـ 100% في القدرات والتحصيلي',
                'description' => 'نظام التدريب التكيفي للرياضيات — محاكاة قياس الواقعي، بنك أسئلة مصنف، خطط ذكية يومية، وتوصيات ذكاء اصطناعي لطلاب الثانوية في المملكة.',
                'primaryKeyword' => 'إمبراطور الرياضيات', 'secondaryKeywords' => ['قدرات', 'تحصيلي', 'محاكي قياس', 'بنك أسئلة', 'رياضيات ثانوي'],
                'ogImage' => '/images/saudi-students-hero.jpg', 'canonical' => "https://{$d}/", 'robots' => 'index,follow', 'type' => 'website', 'schemaType' => 'WebSite',
            ],
            'courses' => [
                'route' => '/#courses', 'title' => 'دورات وشروحات الإمبراطور التأسيسية | منهاج التأسيس والاحتراف لقياس — إمبراطور الرياضيات',
                'description' => 'منهاج التأسيس والاحتراف لاختبارات القدرات والتحصيلي: شروحات تفكك المفاهيم من الصفر، ملخصات PDF مجانية، وتطبيقات عملية فورية لكل فكرة.',
                'primaryKeyword' => 'دورات قدرات', 'secondaryKeywords' => ['شروحات رياضيات', 'معسكر قدرات', 'صقر تحصيلي', 'دورات سعودية'],
                'ogImage' => '/images/course-algebra.jpg', 'canonical' => "https://{$d}/#courses", 'robots' => 'index,follow', 'type' => 'course', 'schemaType' => 'Course',
            ],
            'simulator' => [
                'route' => '/#simulator', 'title' => 'محاكي قياس الواقعي | جرّب محاكاة القدرات والتحصيلي — إمبراطور الرياضيات',
                'description' => 'محاكي قياس مطابق لأجواء الاختبار الفعلي: نفس الأقسام، التوقيت الرسمي، شطب الخيارات، والتصحيح المباشر — جرّبه الآن مجانًا.',
                'primaryKeyword' => 'محاكي القدرات', 'secondaryKeywords' => ['محاكاة قياس', 'اختبار قدرات', 'توقيت قياس', 'قسم كمي'],
                'ogImage' => '/images/tool-simulator.jpg', 'canonical' => "https://{$d}/#simulator", 'robots' => 'index,follow', 'type' => 'website', 'schemaType' => 'WebPage',
            ],
            'plans' => [
                'route' => '/#plans', 'title' => 'الخطط الدراسية الذكية | جداول مبنية عليك — إمبراطور الرياضيات',
                'description' => 'جداول تدريب يومية (٣٠ و٦٠ يومًا) تتكيف مع أدائك في الأسبوع، مع تعديل تلقائي للجدول عند التأخر ومعادلة رقمية حول المهارات الفرعية.',
                'primaryKeyword' => 'خطة قدرات', 'secondaryKeywords' => ['جدول مذاكرة', 'خطة انطلاق', 'خطة حسم', 'الخطط الذكية'],
                'ogImage' => '/images/course-reading.jpg', 'canonical' => "https://{$d}/#plans", 'robots' => 'index,follow', 'type' => 'website', 'schemaType' => 'WebPage',
            ],
            'pricing' => [
                'route' => '/#pricing', 'title' => 'الباقات والمسارات السعرية | اختر باقتك وتوّج درجتك — إمبراطور الرياضيات',
                'description' => 'اختر مسارك المناسب من التأسيسي إلى الاحترافي للقدرات والتحصيلي — كل باقة بمزايا مدروسة للطالب السعودي وسابعة تجهيزه للاختبار بنسبة 100%.',
                'primaryKeyword' => 'باقات قدرات', 'secondaryKeywords' => ['اشتراك رياضيات', 'مسار احترافي', 'مسارمتقدم', 'اشتراك انلاين سعودى؟'],
                'ogImage' => '/images/course-elite.jpg', 'canonical' => "https://{$d}/#pricing", 'robots' => 'index,follow', 'type' => 'website', 'schemaType' => 'WebPage',
            ],
            'faq' => [
                'route' => '/#faq', 'title' => 'الأسئلة الشائعة | إجاباتنا عن المحاكي والباقات والتجميعات — إمبراطور الرياضيات',
                'description' => 'كل ما يدور في ذهنك حول اختبارات القدرات والتحصيلي، المحاكي، باقات السعرية، التجميعات المحدثة، والتزامك سابقًا استعدت للـ١٠٪',
                'primaryKeyword' => 'أسئلة شائعة قياس', 'secondaryKeywords' => ['إجابات قياس', 'الأسئلة الشائعة', 'تجميع قياس', 'إجابات التحصيلي'],
                'ogImage' => '/images/saudi-students-hero.jpg', 'canonical' => "https://{$d}/#faq", 'robots' => 'index,follow', 'type' => 'website', 'schemaType' => 'FAQPage',
            ],
        ];
    }

    /** page config merged with admin overrides (title/description/keywords/robots) */
    public static function resolved(string $key): array
    {
        $page = self::pages()[$key];
        $o = app(AdminStore::class)->setting('seoOverrides', [])[$key] ?? [];
        foreach (['title', 'description', 'primaryKeyword', 'secondaryKeywords', 'robots'] as $f) {
            if (isset($o[$f]) && $o[$f] !== '' && $o[$f] !== []) {
                $page[$f] = $o[$f];
            }
        }

        return $page;
    }

    public static function abs(string $path): string
    {
        return str_starts_with($path, 'http') ? $path : 'https://'.self::DOMAIN.$path;
    }

    public static function organizationSchema(): array
    {
        $d = self::DOMAIN;

        return [
            '@context' => 'https://schema.org', '@type' => 'Organization',
            'name' => 'منصة إمبراطور الرياضيات', 'legalName' => 'منصة إمبراطور الرياضيات — Math Emperor',
            'url' => "https://{$d}/",
            'description' => 'منصة تعليمية سعودية متخصصة في تأهيل طلاب الثانوية لاختباري القدرات العامة والتحصيلي.',
            'logo' => ['@type' => 'ImageObject', 'url' => "https://{$d}/images/saudi-students-hero.jpg", 'width' => 1200, 'height' => 630],
            'address' => ['@type' => 'PostalAddress', 'addressCountry' => 'SA', 'addressRegion' => 'الرياض', 'addressLocality' => 'الرياض'],
            'areaServed' => 'SA', 'foundingDate' => '2024',
            'contactPoint' => ['@type' => 'ContactPoint', 'contactType' => 'customer support', 'telephone' => '+966500523516', 'email' => 'ahmedabdelazem495@gmail.com', 'availableLanguage' => ['ar', 'en']],
        ];
    }

    public static function webSiteSchema(): array
    {
        $d = self::DOMAIN;

        return [
            '@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => 'منصة إمبراطور الرياضيات',
            'url' => "https://{$d}/", 'inLanguage' => ['ar', 'en'],
            'publisher' => ['@type' => 'Organization', 'name' => 'منصة إمبراطور الرياضيات'],
            'potentialAction' => ['@type' => 'SearchAction', 'target' => "https://{$d}/?q={search_term_string}", 'query-input' => 'required name=search_term_string'],
        ];
    }

    public static function faqSchema(): array
    {
        return [
            '@context' => 'https://schema.org', '@type' => 'FAQPage',
            'mainEntity' => array_map(fn ($f) => ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]], Content::faqs()),
        ];
    }

    public static function robotsLines(): array
    {
        $domain = 'https://'.self::DOMAIN;

        return [
            'User-agent: *', 'Allow: /', '',
            '# مناطق خاصة ممنوعة من الفهرسة (Student/Teacher/Admin private surfaces)',
            'Disallow: /admin/', 'Disallow: /dashboard/', 'Disallow: /account/', 'Disallow: /login/',
            'Disallow: /portal/', 'Disallow: /imperial/', 'Disallow: /student-portal/', 'Disallow: /teacher-portal/', 'Disallow: /parent-portal/',
            '', "Sitemap: {$domain}/sitemap.xml",
        ];
    }

    public static function sitemapXml(): string
    {
        $today = now()->toDateString();
        $urls = collect(self::pages())
            ->filter(fn ($p) => $p['robots'] !== 'noindex,nofollow')
            ->map(fn ($p) => "  <url>\n    <loc>{$p['canonical']}</loc>\n    <lastmod>{$today}</lastmod>\n    <changefreq>weekly</changefreq>\n    <priority>".($p['route'] === '/' ? '1.0' : '0.8')."</priority>\n  </url>")
            ->implode("\n");

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$urls}\n</urlset>";
    }

    /** AdSense publisher id gate: ca-pub- + 12–18 digits */
    public static function isValidPublisherId(?string $id): bool
    {
        $raw = strtolower(trim((string) $id));

        return (bool) preg_match('/^ca-pub-\d{12,18}$/', $raw);
    }
}
