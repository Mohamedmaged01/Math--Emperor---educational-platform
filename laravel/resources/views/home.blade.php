<x-layouts.app :seo="$seo">
    @push('head')
        @if ($adsensePublisher)
            <script async crossorigin="anonymous"
                src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={{ urlencode($adsensePublisher) }}"></script>
        @endif
    @endpush

    <div class="grain relative min-h-screen overflow-x-clip bg-background font-sans text-foreground">
        {{-- scroll progress --}}
        <div data-scroll-progress aria-hidden="true"
            class="scroll-progress fixed inset-x-0 top-0 z-[70] h-[3px] origin-right bg-gradient-to-l from-secondary-500 via-primary-500 to-primary-700"></div>

        @include('partials.home.navbar')

        <main>
            @include('partials.home.hero')
            @include('partials.home.marquee')
            @include('partials.home.features')
            @include('partials.home.courses')
            @include('partials.home.simulator')
            @include('partials.home.plans')
            @include('partials.home.testimonials')
            @include('partials.home.pricing')
            @include('partials.home.stats')
            @include('partials.home.faq')
            @include('partials.home.final-cta')
        </main>

        @include('partials.home.footer')

        {{-- platform overlays --}}
        @include('partials.overlays.smart-scroll')
        @include('partials.overlays.advisor')
        @include('partials.overlays.site-search')
        @include('partials.overlays.auth')
        @include('partials.overlays.legal')
        @include('partials.overlays.about')
        @include('partials.overlays.contact')
        @include('partials.overlays.question-bank')
    </div>

    @if ($openPortal)
        @push('scripts')
            <script>window.addEventListener('load', () => window.dispatchEvent(new CustomEvent('emperor:open-{{ $openPortal === 'imperial' ? 'imperial' : 'student' }}')));</script>
        @endpush
    @endif
</x-layouts.app>
