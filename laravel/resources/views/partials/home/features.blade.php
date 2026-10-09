@php
    use App\Support\Content;
    $tools = Content::tools();
    $chips = Content::heroChips();
@endphp
<section id="features" x-data="{ open: false, current: null, tools: @js($tools) }" class="relative overflow-hidden py-20 sm:py-28">
    <div class="absolute top-1/4 -left-40 h-[380px] w-[380px] rounded-full bg-primary-100/60 blur-[130px] dark:bg-primary-500/8" aria-hidden="true"></div>
    <div class="absolute top-10 right-1/2 h-64 w-[720px] translate-x-1/2 rounded-full bg-accent-100/40 blur-[120px] dark:bg-accent-500/6" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6">

        {{-- ====== signature header block ====== --}}
        <div class="relative mx-auto max-w-4xl text-center">
            {{-- floating math ornaments --}}
            <span aria-hidden="true" data-reveal style="--ry:0px;--rs:.6;--rd:.8s;--rdelay:.4s" class="absolute -top-4 start-0 hidden select-none md:block">
                <span class="block rotate-[-8deg] rounded-2xl border border-primary-100 bg-surface px-4 py-2 shadow-md"><span class="equation text-primary-400">Σ xᵢ / n</span></span>
            </span>
            <span aria-hidden="true" data-reveal style="--ry:0px;--rs:.6;--rd:.8s;--rdelay:.55s" class="absolute -top-2 end-0 hidden select-none md:block">
                <span class="block rotate-[7deg] rounded-2xl border border-secondary-100 bg-surface px-4 py-2 shadow-md"><span class="equation text-secondary">Δf(x) = f′(x)</span></span>
            </span>
            <span aria-hidden="true" data-reveal style="--ry:0px;--rs:.6;--rd:.8s;--rdelay:.7s" class="absolute top-24 -end-7 hidden select-none md:block">
                <span class="block rotate-[-5deg] rounded-full border border-accent-100 bg-surface px-3.5 py-1.5 shadow-md"><span class="equation text-accent">π ≈ 3.14159</span></span>
            </span>

            {{-- badge --}}
            <div data-reveal style="--ry:16px;--rs:.94;--rd:.65s" class="relative inline-block">
                <span class="animate-pulse-ring absolute inset-0 rounded-full ring-1 ring-primary-400/40" aria-hidden="true"></span>
                <x-badge tone="primary" icon="zap" class="relative px-5 py-2.5 shadow-md shadow-primary-500/10">
                    منظومة أدوات الإمبراطور للتدريب التكتيكي الذكي
                </x-badge>
            </div>

            {{-- mega title --}}
            <div class="relative mx-auto mt-7 max-w-2xl">
                <h2 data-reveal style="--ry:28px;--rd:.85s;--rdelay:.1s" class="text-headline text-foreground">
                    كل ما تحتاجه للوصول للـ
                    <span class="mx-2 inline-flex -skew-x-6 rounded-xl bg-gradient-to-l from-amber-300 to-amber-500 px-3 pb-0.5 text-amber-950 shadow-md shadow-amber-400/30">100%</span>
                </h2>
                <p data-reveal style="--ry:24px;--rd:.85s;--rdelay:.22s" class="gradient-text mt-2 py-1 text-display">في مكان واحد</p>
                <span aria-hidden="true" data-reveal="line" style="--rd:1s;--rdelay:.4s" class="section-line mx-auto mt-4 block h-1 w-56 rounded-full"></span>
            </div>

            {{-- desc --}}
            <div data-reveal style="--ry:20px;--rd:.8s;--rdelay:.32s"
                class="mx-auto mt-6 max-w-2xl rounded-2xl border border-border bg-surface/70 p-4 shadow-sm backdrop-blur-sm sm:p-5">
                <p class="text-body-lg leading-relaxed text-foreground-2">
                    اختر الأداة المناسبة لجدولك اليومي: من
                    <span class="font-black text-foreground">المحاكاة الواقعية</span>، إلى
                    <span class="font-black text-foreground">خزينة النماذج</span>، و<span class="font-black text-foreground">الخطط المجدولة</span>، و<span class="font-black text-foreground">حسابات القبول الجامعي</span>.
                </p>
            </div>
        </div>

        {{-- available strip --}}
        <div data-reveal style="--ry:18px;--rd:.7s" class="mx-auto mt-8 flex flex-wrap items-center justify-center gap-2">
            <x-badge tone="success" icon="shield-check">متاحة بالكامل لجميع الطلاب</x-badge>
            @foreach (array_slice($chips, 1) as $c)
                <x-badge tone="neutral" size="sm">{{ $c }}</x-badge>
            @endforeach
        </div>

        {{-- tools grid --}}
        <div class="mt-14 grid gap-5 sm:grid-cols-2">
            @foreach ($tools as $i => $t)
                <article data-reveal style="--ry:36px;--rd:.75s;--rdelay:{{ ($i % 2) * 0.12 }}s"
                    class="group relative overflow-hidden rounded-3xl border border-border bg-surface shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-primary-200 hover:shadow-xl">
                    <div class="relative h-40 overflow-hidden text-white sm:h-44">
                        <img src="{{ $t['image'] }}" alt="{{ $t['imgAlt'] }}" loading="lazy"
                            class="size-full object-cover transition-transform duration-[1.2s] ease-out group-hover:scale-[1.06]" />
                        <div class="absolute inset-0 bg-gradient-to-t from-foreground/70 via-foreground/15 to-transparent" aria-hidden="true"></div>
                        <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t opacity-60 mix-blend-multiply {{ $t['accent'] }}" aria-hidden="true"></div>
                        <div class="absolute inset-x-4 top-3.5 flex items-start justify-between gap-2">
                            <div>
                                <p class="text-[9px] font-black tracking-wider text-white/80">{{ $t['tag'] }}</p>
                                <p class="mt-0.5 inline-flex items-center gap-1.5 rounded-full bg-white/18 px-2.5 py-1 text-[10px] font-black backdrop-blur-sm">{{ $t['tagMeta'] }}</p>
                            </div>
                            <span class="grid size-11 place-items-center rounded-2xl bg-white/18 ring-1 ring-white/30 backdrop-blur-sm">
                                <x-i :name="$t['icon']" class="size-5.5" />
                            </span>
                        </div>
                        <x-badge tone="neutral" size="sm" class="absolute bottom-3 start-4 border-white/25 bg-white/15 text-white backdrop-blur-sm">{{ $t['badge'] }}</x-badge>
                    </div>

                    <div class="p-5 sm:p-6">
                        <h3 class="text-subtitle text-foreground">{{ $t['title'] }}</h3>
                        <p class="mt-1.5 text-small leading-relaxed text-foreground-2">{{ $t['desc'] }}</p>
                        <button type="button" @click="current = tools[{{ $i }}]; open = true" aria-label="{{ $t['cta'] }} — {{ $t['title'] }}"
                            class="group/cta mt-5 inline-flex items-center gap-1.5 text-small font-black text-primary transition-colors hover:text-primary-hover">
                            {{ $t['cta'] }}
                            <x-i name="chevron-left" class="size-4.5 transition-transform duration-300 group-hover/cta:-translate-x-1" />
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    {{-- ===== tool modal ===== --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak x-effect="$store.lock.set('tool-modal', !!open)" @keydown.escape.window="open = false"
            x-transition:leave="duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-100">
            <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 z-[75] bg-foreground/45 backdrop-blur-sm"></div>
            <div x-show="open"
                x-transition:enter="transition duration-[350ms] ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="opacity-0 translate-y-8 scale-[0.96]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-6 scale-[0.97]"
                role="dialog" aria-modal="true" :aria-label="current?.title"
                class="fixed inset-x-0 top-[6%] z-[80] mx-auto flex max-h-[86vh] w-[min(760px,94vw)] flex-col overflow-hidden rounded-3xl border border-border bg-surface shadow-2xl">
                <template x-if="current">
                    <div class="flex min-h-0 flex-1 flex-col">
                        {{-- head --}}
                        <div class="relative shrink-0 overflow-hidden px-6 py-6 text-white sm:px-8">
                            <img :src="current.image" :alt="current.imgAlt" class="absolute inset-0 size-full object-cover" aria-hidden="true" />
                            <div class="absolute inset-0 bg-gradient-to-br opacity-80 mix-blend-multiply" :class="current.accent" aria-hidden="true"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-foreground/70 via-transparent to-foreground/20" aria-hidden="true"></div>
                            <button type="button" @click="open = false" aria-label="إغلاق"
                                class="absolute top-4 left-4 grid size-9 place-items-center rounded-xl bg-white/15 text-white transition-colors hover:bg-white/25">
                                <x-i name="x" class="size-4.5" />
                            </button>
                            <div class="relative flex items-center gap-4">
                                <span class="grid size-14 place-items-center rounded-2xl bg-white/15 ring-1 ring-white/30 backdrop-blur">
                                    @foreach ($tools as $t)
                                        <span x-show="current.id === '{{ $t['id'] }}'"><x-i :name="$t['icon']" class="size-7" /></span>
                                    @endforeach
                                </span>
                                <div>
                                    <p class="text-[9px] font-black tracking-wider text-white/75" x-text="current.tag + ' · ' + current.tagMeta"></p>
                                    <h2 class="text-title text-white" x-text="current.title"></h2>
                                </div>
                            </div>
                            <p class="relative mt-3 text-small font-bold text-white/95" x-text="current.modal.headline"></p>
                        </div>

                        {{-- body --}}
                        <div class="flex-1 overflow-y-auto p-6 sm:p-8">
                            <ul class="space-y-3.5">
                                <template x-for="(p, i) in current.modal.points" :key="p.t">
                                    <li class="anim-slide-x flex gap-3.5 rounded-2xl border border-border bg-surface-2/60 p-4" :style="'animation-delay:' + (100 + i * 80) + 'ms'">
                                        <span class="num grid size-8 shrink-0 place-items-center rounded-xl bg-primary-soft text-[11px] font-black text-primary" dir="ltr" x-text="String(i + 1).padStart(2, '0')"></span>
                                        <div>
                                            <p class="text-small font-black text-foreground" x-text="p.t"></p>
                                            <p class="mt-1 text-xs font-bold leading-relaxed text-foreground-2" x-text="p.d"></p>
                                        </div>
                                    </li>
                                </template>
                            </ul>

                            <div class="mt-6 flex items-start gap-3 rounded-2xl border border-secondary-100 bg-secondary-soft/60 p-4">
                                <x-i name="shield-check" class="mt-0.5 size-5 shrink-0 text-secondary" />
                                <p class="text-small font-black text-foreground"><span class="text-secondary">ضمان الجودة:</span> <span x-text="current.modal.footnote"></span></p>
                            </div>

                            <button type="button" @click="open = false"
                                class="group/btn mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-5 py-4 text-base font-black text-white shadow-lg shadow-primary-500/25 transition-all hover:brightness-110 active:scale-[0.99]">
                                <x-i name="zap" class="size-5" />
                                <span x-text="current.modal.previewLabel"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>
</section>
