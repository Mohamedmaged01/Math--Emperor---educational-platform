@php $fade = fn (int $i) => '--ry:34px;--rd:.9s;--rdelay:'.(0.15 + $i * 0.12).'s'; @endphp
<section id="top" x-data="heroParallax" @mousemove="move($event)" class="relative overflow-hidden pt-32 pb-20 sm:pt-40 lg:pb-28">
    {{-- background — light-first, dark-capable --}}
    <div class="grid-bg absolute inset-0" aria-hidden="true"></div>
    <div class="absolute -top-40 left-1/2 h-[520px] w-[880px] -translate-x-1/2 rounded-full bg-primary-200/45 blur-[130px] dark:bg-primary-500/10" aria-hidden="true"></div>
    <div class="absolute top-1/3 -right-40 h-[400px] w-[400px] rounded-full bg-secondary-100/60 blur-[120px] dark:bg-secondary-500/8" aria-hidden="true"></div>
    <div class="absolute bottom-0 -left-40 h-[360px] w-[360px] rounded-full bg-accent-100/50 blur-[120px] dark:bg-accent-500/8" aria-hidden="true"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:gap-10">
        {{-- ---------------- copy ---------------- --}}
        <div class="text-center lg:text-right">
            <div data-reveal style="{{ $fade(0) }}">
                <x-badge tone="primary" icon="sigma">
                    محرّك التعلّم التكيفي للرياضيات — قدرات وتحصيلي
                    <span class="relative flex size-2" aria-hidden="true">
                        <span class="absolute inline-flex size-full animate-ping rounded-full bg-secondary-500 opacity-70"></span>
                        <span class="relative inline-flex size-2 rounded-full bg-secondary-500"></span>
                    </span>
                </x-badge>
            </div>

            <h1 data-reveal style="{{ $fade(1) }}" class="mt-6 text-display text-foreground">
                {{ $hero['line1'] }}
                <span class="gradient-text block py-1">{{ $hero['line2'] }}</span>
                {{ $hero['line3'] }}
            </h1>

            <p data-reveal style="{{ $fade(2) }}" class="mx-auto mt-5 max-w-xl text-body-lg text-foreground-2 lg:mx-0">
                محاكاة قياس واقعية، بنك أسئلة ذكي يتجاوز ١٢٬٥٠٠ سؤال، وخطط تعلّم
                تتكيّف مع مستواك — كل ما تحتاجه لتحويل الرياضيات من قلق إلى ثقة.
            </p>

            <div data-reveal style="{{ $fade(3) }}" class="mt-8 flex flex-col items-center gap-3.5 sm:flex-row sm:justify-center lg:justify-start">
                <a href="#simulator"
                    class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-8 py-4 text-base font-black text-white shadow-lg shadow-primary-500/25 transition-all duration-200 hover:shadow-xl hover:shadow-primary-500/35 hover:brightness-110 active:scale-[0.99] sm:w-auto">
                    جرّب المحاكي الآن
                    <x-i name="chevron-left" class="size-5 transition-transform duration-300 group-hover:-translate-x-1" />
                </a>
                <a href="#features"
                    class="group inline-flex w-full items-center justify-center gap-2.5 rounded-xl border border-border-strong bg-surface px-8 py-4 text-base font-black text-foreground shadow-xs transition-all duration-200 hover:border-primary-300 hover:text-primary sm:w-auto">
                    <span class="grid size-8 place-items-center rounded-full bg-primary-soft text-primary transition-transform duration-300 group-hover:scale-110">
                        <x-i name="play" class="size-3.5 fill-current" />
                    </span>
                    اكتشف الأدوات
                </a>
            </div>

            {{-- social proof --}}
            <div data-reveal style="{{ $fade(4) }}" class="mt-9 flex flex-col items-center gap-4 sm:flex-row sm:justify-center lg:justify-start">
                <div class="flex -space-x-3 space-x-reverse">
                    @foreach (['ع', 'س', 'ف', 'ن', 'م'] as $i => $ch)
                        <span class="grid size-10 place-items-center rounded-full border-2 border-surface bg-surface-2 text-sm font-black text-primary shadow-sm" style="z-index: {{ 5 - $i }}">{{ $ch }}</span>
                    @endforeach
                    <span class="num grid size-10 place-items-center rounded-full border-2 border-surface bg-gradient-to-br from-primary-500 to-primary-700 text-[10px] font-black text-white shadow-sm">+142K</span>
                </div>
                <div class="text-center sm:text-right">
                    <div class="flex items-center justify-center gap-1 sm:justify-start">
                        @for ($i = 0; $i < 5; $i++)
                            <x-i name="star" class="size-4 fill-amber-400 text-amber-400" />
                        @endfor
                        <span class="num mr-1 text-small font-black text-foreground">4.9</span>
                    </div>
                    <p class="text-caption text-foreground-3">تقييم أكثر من ١٤٢ ألف طالب وطالبة</p>
                </div>
            </div>
        </div>

        {{-- ---------------- visual: Saudi secondary students ---------------- --}}
        <div data-reveal style="--ry:40px;--rs:.94;--rd:1.1s;--rdelay:.35s" class="relative mx-auto w-full max-w-[560px]">
            <figure class="parallax-img group relative z-10">
                <div class="relative overflow-hidden rounded-[1.75rem] border border-border bg-surface shadow-2xl">
                    <img src="/images/saudi-students-hero.jpg"
                        alt="طلاب الثانوية السعوديون يدرسون الرياضيات معًا — طالبان بالثوب والشماغ وطالبة بالعباءة حول كراس معادلات في فصل حديث"
                        loading="eager"
                        class="aspect-[4/3.4] w-full object-cover transition-transform duration-[1.5s] ease-out group-hover:scale-[1.045] sm:aspect-[4/3]" />
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-foreground/40 via-transparent to-transparent" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-0 rounded-[1.75rem] ring-1 ring-inset ring-white/25" aria-hidden="true"></div>

                    <span class="glass absolute top-4 start-4 inline-flex items-center gap-2 rounded-xl px-3.5 py-2 text-[11px] font-black text-white shadow-lg">
                        <x-i name="graduation-cap" class="size-4 text-amber-300" />
                        طلابنا — المرحلة الثانوية
                    </span>

                    <figcaption class="absolute bottom-4 start-4 text-start text-[11px] font-bold leading-relaxed text-white/95 drop-shadow-sm sm:text-xs">
                        من فصول المملكة إلى قاعة قياس — طريق واحد نحو الـ 100%.
                    </figcaption>
                </div>
                <div class="absolute -inset-6 -z-10 rounded-[2.5rem] bg-primary-200/40 blur-3xl dark:bg-primary-500/10" aria-hidden="true"></div>
            </figure>

            {{-- floating score badge --}}
            <div class="parallax-badge absolute -top-7 -start-2 z-20 sm:-start-8">
                <div class="anim-bob-up relative rounded-2xl border border-primary-100 bg-surface px-5 py-4 shadow-xl shadow-primary-500/10">
                    <span class="animate-pulse-ring absolute inset-0 rounded-2xl ring-1 ring-primary-400/40" aria-hidden="true"></span>
                    <div class="flex items-center gap-3">
                        <span class="grid size-11 place-items-center rounded-full bg-gradient-to-br from-primary-400 to-primary-700 text-white shadow-md">
                            <x-i name="crown" class="size-5.5" stroke-width="2.4" />
                        </span>
                        <div>
                            <p class="text-[10px] font-bold text-foreground-3">درجتك المتوقعة</p>
                            <p class="num text-2xl font-black leading-none text-foreground">99<span class="text-sm text-primary">%</span></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- floating verified badge --}}
            <div class="parallax-badge absolute -bottom-6 -end-2 z-20 sm:-end-8">
                <div class="anim-bob-down flex items-center gap-3 rounded-2xl border border-border bg-surface px-5 py-3.5 shadow-lg">
                    <span class="grid size-10 place-items-center rounded-full bg-success-soft text-success">
                        <x-i name="badge-check" class="size-5" />
                    </span>
                    <div>
                        <p class="text-xs font-black text-foreground">نورة حققت ١٠٠٪</p>
                        <p class="text-[10px] font-semibold text-foreground-3">قبل ٣ دقائق — القدرات العامة</p>
                    </div>
                </div>
            </div>

            {{-- floating grad cap --}}
            <div class="parallax-cap absolute top-1/2 -end-10 z-0 hidden lg:block">
                <span class="anim-wobble grid size-18 place-items-center rounded-3xl border border-border bg-surface text-primary-300 shadow-lg">
                    <x-i name="graduation-cap" class="size-8" />
                </span>
            </div>
        </div>
    </div>
</section>
