@php $pricing = \App\Support\Content::pricing(); @endphp
<section id="pricing" class="relative overflow-hidden py-20 sm:py-28">
    <div class="absolute top-10 right-1/2 h-[380px] w-[680px] translate-x-1/2 rounded-full bg-primary-100/70 blur-[140px] dark:bg-primary-500/8" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
        <x-section-heading badge="الاستثمار في مستقبلك" title="اختر باقتك —" :inline="true" gold-title="وتوّج درجتك"
            desc="ابدأ مجانًا، وارتقِ متى ما جهزت. إلغاء في أي وقت، ودفع آمن عبر جميع الوسائل." />

        <div class="mt-14 grid items-stretch gap-5 lg:grid-cols-3">
            @foreach ($pricing as $i => $p)
                <article data-reveal style="--ry:44px;--rd:.8s;--rdelay:{{ $i * 0.12 }}s"
                    @class([
                        'relative flex flex-col overflow-hidden rounded-3xl p-7 transition-all duration-300 sm:p-8',
                        'border-2 border-primary-500 bg-surface shadow-xl shadow-primary-500/15 lg:-my-3 lg:py-11' => $p['featured'],
                        'border border-border bg-surface shadow-sm hover:shadow-md' => ! $p['featured'],
                    ])>
                    @if ($p['featured'])
                        <span class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-l from-primary-400 via-primary-600 to-secondary-500" aria-hidden="true"></span>
                        <span class="absolute top-5 left-5 inline-flex items-center gap-1.5 rounded-full bg-gradient-to-l from-primary-500 to-primary-700 px-3.5 py-1.5 text-[10px] font-black text-white shadow-md shadow-primary-500/25">
                            <x-i name="crown" class="size-3.5" />
                            الأكثر اشتراكًا
                        </span>
                    @endif

                    <div class="flex items-center gap-3">
                        <span @class(['grid size-12 place-items-center rounded-2xl border', 'border-primary-200 bg-primary-soft text-primary' => $p['featured'], 'border-border bg-surface-2 text-foreground-2' => ! $p['featured']])>
                            <x-i :name="$p['icon']" class="size-6" />
                        </span>
                        <h3 class="text-title text-foreground">{{ $p['name'] }}</h3>
                    </div>

                    <p class="mt-2.5 text-xs font-bold text-foreground-3">{{ $p['desc'] }}</p>

                    <div class="mt-6 flex items-end gap-2">
                        @if ($p['price'] === 'مجانًا')
                            <span class="text-4xl font-black text-foreground">مجانًا</span>
                        @else
                            <span class="num text-5xl font-black text-foreground">{{ $p['price'] }}</span>
                            <span class="pb-1.5 text-small font-bold text-foreground-3">{{ $p['currency'] }} / {{ $p['period'] }}</span>
                        @endif
                    </div>

                    <ul class="mt-7 flex-1 space-y-3">
                        @foreach ($p['features'] as $f)
                            <li class="flex items-start gap-2.5 text-small font-semibold text-foreground-2">
                                <span @class(['mt-0.5 grid size-5 shrink-0 place-items-center rounded-full', 'bg-primary-500 text-white' => $p['featured'], 'bg-secondary-soft text-secondary' => ! $p['featured']]) aria-hidden="true">
                                    <x-i name="check" class="size-3" stroke-width="3.5" />
                                </span>
                                {{ $f }}
                            </li>
                        @endforeach
                    </ul>

                    <a href="#top" @click.prevent aria-disabled="true"
                        @class([
                            'mt-8 inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3.5 text-small font-black transition-all duration-200 active:scale-[0.99]',
                            'bg-gradient-to-l from-primary-500 to-primary-700 text-white shadow-lg shadow-primary-500/30 hover:brightness-110' => $p['featured'],
                            'border border-border-strong bg-surface text-foreground hover:border-primary-300 hover:text-primary' => ! $p['featured'],
                        ])>
                        @if ($p['featured'])<x-i name="sparkles" class="size-4" />@endif
                        {{ $p['cta'] }}
                    </a>
                </article>
            @endforeach
        </div>

        <div data-reveal style="--ry:20px;--rd:.7s;--rdelay:.3s" class="mt-10 flex flex-wrap items-center justify-center gap-x-8 gap-y-3 text-xs font-bold text-foreground-3">
            @foreach (['دفع آمن ١٠٠٪', 'إلغاء في أي وقت', 'مدى · فيزا · Apple Pay', 'فواتير للشركات'] as $t)
                <span class="inline-flex items-center gap-2"><x-i name="shield-check" class="size-4 text-success" />{{ $t }}</span>
            @endforeach
        </div>
    </div>
</section>
