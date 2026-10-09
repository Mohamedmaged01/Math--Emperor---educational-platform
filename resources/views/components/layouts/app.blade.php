@props(['seo' => null, 'title' => null, 'robots' => null, 'bodyClass' => ''])
@php
    use App\Support\Seo;
    $page = $seo ?? Seo::pages()['home'];
    $pageTitle = $title ?? $page['title'];
    $canonical = preg_replace('/(\.sa)\/+$/', '$1/', $page['canonical']);
    $keywords = implode(', ', array_merge([$page['primaryKeyword']], (array) $page['secondaryKeywords']));
    $robotsValue = $robots ?? ($page['robots'] === 'noindex,nofollow' ? 'noindex, nofollow' : 'index, follow');
@endphp
<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $page['description'] }}" />
    <meta name="robots" content="{{ $robotsValue }}" />
    <meta name="author" content="منصة إمبراطور الرياضيات" />
    <meta name="generator" content="Math Emperor SEO Engine" />
    <meta name="keywords" content="{{ $keywords }}" />
    <link rel="canonical" href="{{ $canonical }}" />
    <meta property="og:title" content="{{ $page['title'] }}" />
    <meta property="og:description" content="{{ $page['description'] }}" />
    <meta property="og:url" content="{{ $canonical }}" />
    <meta property="og:type" content="{{ $page['type'] }}" />
    <meta property="og:image" content="{{ Seo::abs($page['ogImage']) }}" />
    <meta property="og:image:alt" content="{{ $page['title'] }} — صورة معبرة عن {{ $page['primaryKeyword'] }}" />
    <meta property="og:site_name" content="منصة إمبراطور الرياضيات" />
    <meta property="og:locale" content="ar_SA" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $page['title'] }}" />
    <meta name="twitter:description" content="{{ $page['description'] }}" />
    <meta name="twitter:image" content="{{ Seo::abs($page['ogImage']) }}" />
    @if (($page['schemaType'] ?? null) === 'WebSite')
        <script type="application/ld+json">{!! json_encode(Seo::organizationSchema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
        <script type="application/ld+json">{!! json_encode(Seo::webSiteSchema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
    @endif

    {{-- theme: resolve before first paint (light default · dark · system) --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('math-emperor-theme') || 'light';
                var dark = t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
                document.documentElement.classList.toggle('dark', dark);
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

    <script>
        window.EMPEROR = {!! json_encode([
            'auth' => auth()->check() ? ['role' => auth()->user()->role, 'name' => auth()->user()->name] : null,
            'routes' => ['student' => route('student.portal'), 'imperial' => route('imperial.show'), 'home' => url('/')],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!};
    </script>
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-foreground antialiased font-sans {{ $bodyClass }}">
    {{ $slot }}
    @stack('scripts')
</body>
</html>
