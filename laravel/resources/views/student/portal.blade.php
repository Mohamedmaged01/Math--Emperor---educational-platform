@php
    $seo = \App\Support\Seo::pages()['home'];
    $stats = [
        ['icon' => 'line-chart', 'label' => 'متوسط درجاتك', 'value' => $avg.'%', 'note' => 'متوسط آخر محاكيات'],
        ['icon' => 'award', 'label' => 'نقاط الإنجاز', 'value' => number_format($user->points), 'note' => '+٢٥ لكل إجابة صحيحة'],
        ['icon' => 'flame', 'label' => 'سلسلة المواظبة', 'value' => $user->streak.' أيام', 'note' => 'استمر!'],
        ['icon' => 'target', 'label' => 'هدفك الحالي', 'value' => '95+', 'note' => 'قدرات — الدورة القادمة'],
    ];
    $barTone = fn ($c, $pct) => $pct === 100 ? 'bg-gradient-to-l from-amber-400 to-amber-500'
        : match ($c['tone']) {
            'secondary' => 'bg-gradient-to-l from-secondary-500 to-secondary-600',
            'accent' => 'bg-gradient-to-l from-accent-500 to-accent-600',
            default => 'bg-gradient-to-l from-primary-400 to-primary-600',
        };
@endphp
<x-layouts.app :seo="$seo" title="بوابة الطالب — منصة الإمبراطور" robots="noindex, nofollow">
    <div class="min-h-screen bg-background">
        {{-- header --}}
        <header class="sticky top-0 z-10 border-b border-white/10 bg-gradient-to-l from-secondary-600 via-secondary-700 to-[#0c4f47] text-white">
            <div class="mx-auto flex max-w-6xl items-center gap-3 px-4 py-4 sm:px-6">
                <span class="grid size-11 place-items-center rounded-2xl bg-white/12 ring-1 ring-white/25 backdrop-blur">
                    <x-i name="layout-dashboard" class="size-5.5" />
                </span>
                <div class="flex-1 leading-tight">
                    <h1 class="text-base font-black sm:text-lg">بوابة الطالب — منصة الإمبراطور</h1>
                    <p class="text-[11px] font-bold text-white/75">مساحتك الشخصية لمتابعة درجات المحاكاة وتجميعاتك · {{ $user->name }}</p>
                </div>
                <div class="hidden items-center gap-2 rounded-xl bg-white/10 px-3.5 py-2 text-xs font-bold sm:flex">
                    <x-i name="user-round" class="size-4" />
                    {{ $user->name }}
                </div>
                <a href="{{ url('/') }}" aria-label="الصفحة الرئيسية" title="الصفحة الرئيسية — جلستك تبقى نشطة"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-white/25 bg-white/10 px-3.5 py-2 text-xs font-black transition-colors hover:bg-white/18">
                    <x-i name="house" class="size-4" />
                    <span class="hidden sm:inline">الصفحة الرئيسية</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border border-white/25 bg-white/10 px-3.5 py-2 text-xs font-black transition-colors hover:bg-white/18">
                        <x-i name="log-out" class="size-4" />
                        خروج
                    </button>
                </form>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
            {{-- stats row --}}
            <div class="grid grid-cols-2 gap-3.5 lg:grid-cols-4">
                @foreach ($stats as $i => $s)
                    <div class="anim-rise rounded-2xl border border-secondary-100/70 bg-secondary-soft/60 p-4.5 shadow-sm sm:p-5" style="animation-delay: {{ 80 + $i * 70 }}ms">
                        <span class="grid size-10 place-items-center rounded-xl bg-white/80 text-secondary shadow-xs"><x-i :name="$s['icon']" class="size-5" /></span>
                        <p class="num mt-3 text-2xl font-black text-foreground sm:text-[1.7rem]">{{ $s['value'] }}</p>
                        <p class="text-caption text-foreground-3">{{ $s['label'] }}</p>
                        <p class="mt-0.5 text-[10px] font-bold text-success">{{ $s['note'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 grid gap-5 lg:grid-cols-3">
                {{-- records --}}
                <section class="anim-rise lg:col-span-2" style="animation-delay: 250ms">
                    <x-card :padded="false" class="overflow-hidden">
                        <div class="flex items-center justify-between border-b border-border px-5 py-4">
                            <h2 class="text-subtitle text-foreground">سجل درجات المحاكاة</h2>
                            <x-badge tone="primary" size="sm">{{ $records->count() }} محاكاة مسجلة</x-badge>
                        </div>
                        @if ($records->isEmpty())
                            <div class="p-5">
                                <div class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-border-strong bg-surface-2/50 px-6 py-14 text-center">
                                    <span class="grid size-14 place-items-center rounded-2xl bg-surface text-foreground-3 shadow-xs"><x-i name="timer" class="size-6" /></span>
                                    <h3 class="text-subtitle text-foreground">لا توجد محاكيات بعد</h3>
                                    <p class="max-w-sm text-small text-foreground-2">أكمل أول اختبار قصير من الصفحة الرئيسية وستظهر نتيجتك هنا مباشرة.</p>
                                </div>
                            </div>
                        @else
                            <ul class="divide-y divide-border">
                                @foreach ($records as $i => $r)
                                    <li class="anim-slide-x flex items-center gap-4 px-5 py-3.5" style="animation-delay: {{ $i * 60 }}ms">
                                        <span @class([
                                            'num grid size-12 shrink-0 place-items-center rounded-2xl text-sm font-black',
                                            'bg-gradient-to-br from-amber-100 to-amber-200 text-amber-700 dark:from-amber-500/20 dark:to-amber-600/20 dark:text-amber-300' => $r->score >= 90,
                                            'bg-primary-soft text-primary' => $r->score >= 75 && $r->score < 90,
                                            'bg-surface-2 text-foreground-2' => $r->score < 75,
                                        ])>{{ $r->score }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-small font-black text-foreground">{{ $r->label }}</p>
                                            <p class="num text-[11px] font-bold text-foreground-3">{{ $r->correct }}/{{ $r->total }} صحيحة · {{ $r->taken_on->format('Y-m-d') }}</p>
                                        </div>
                                        @if ($r->score >= 90)
                                            <x-i name="medal" class="size-5 shrink-0 text-amber-500" aria-label="امتياز ذهبي" />
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </x-card>

                    {{-- weekly trend --}}
                    <x-card class="mt-5">
                        <div class="flex items-center justify-between">
                            <h3 class="text-subtitle text-foreground">منحنى الدقة الأسبوعي</h3>
                            <span class="inline-flex items-center gap-1 text-[11px] font-black text-success"><x-i name="trending-up" class="size-4" /> +١٢٪</span>
                        </div>
                        <div class="mt-4 flex h-28 items-end gap-2" dir="ltr" aria-label="رسم بياني أسبوعي">
                            @foreach ($weekly as $i => $h)
                                <div data-hbar style="--h: {{ $h }}%; --rdelay: {{ 0.35 + $i * 0.07 }}s; --rd: .6s" title="اليوم {{ $i + 1 }}: {{ $h }}٪"
                                    @class([
                                        'flex-1 rounded-t-lg',
                                        'bg-gradient-to-t from-primary-600 to-primary-400 shadow-md shadow-primary-500/25' => $i === count($weekly) - 1,
                                        'bg-surface-3 transition-colors hover:bg-primary-200 dark:hover:bg-primary-800' => $i !== count($weekly) - 1,
                                    ])></div>
                            @endforeach
                        </div>
                        <div class="mt-2 flex justify-between text-[10px] font-black text-foreground-3" dir="ltr">
                            @foreach (['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'] as $d)
                                <span class="flex-1 text-center">{{ $d }}</span>
                            @endforeach
                        </div>
                    </x-card>
                </section>

                {{-- collections --}}
                <section class="anim-rise" style="animation-delay: 330ms">
                    <x-card>
                        <div class="flex items-center gap-2.5">
                            <span class="grid size-10 place-items-center rounded-xl bg-accent-soft text-accent"><x-i name="graduation-cap" class="size-5" /></span>
                            <h3 class="text-subtitle text-foreground">تجميعاتك</h3>
                        </div>
                        <div class="mt-5 space-y-4">
                            @foreach ($collections as $i => $c)
                                @php $pct = (int) round($c['done'] / $c['total'] * 100); @endphp
                                <div>
                                    <div class="mb-1.5 flex items-center justify-between text-xs font-black">
                                        <span class="text-foreground-2">{{ $c['name'] }}</span>
                                        <span class="num text-foreground-3">{{ $pct }}%</span>
                                    </div>
                                    <div class="h-2 overflow-hidden rounded-full bg-surface-3">
                                        <div data-bar style="--w: {{ $pct }}%; --rdelay: {{ 0.4 + $i * 0.12 }}s" class="h-full rounded-full {{ $barTone($c, $pct) }}"></div>
                                    </div>
                                    <p class="num mt-1 text-[10px] font-bold text-foreground-3">{{ $c['done'] }}/{{ $c['total'] }} سؤالًا {{ $pct === 100 ? '· مكتملة ذهبيًا' : '' }}</p>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ url('/#simulator') }}"
                            class="group mt-6 flex items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-secondary-500 to-secondary-700 px-5 py-3.5 text-small font-black text-white shadow-md shadow-secondary-500/25 transition-all hover:brightness-110">
                            <x-i name="sparkles" class="size-4" />
                            ابدأ محاكاة جديدة
                            <x-i name="chevron-left" class="size-4.5 transition-transform group-hover:-translate-x-1" />
                        </a>
                    </x-card>
                </section>
            </div>

            <footer class="mt-10 flex items-center justify-between border-t border-border pt-5 text-[11px] font-bold text-foreground-3">
                <span class="num">MATH EMPEROR · Student Portal v2</span>
                <span class="inline-flex items-center gap-1.5"><x-i name="arrow-right" class="size-3.5" /> مساحتك الشخصية — بياناتك محفوظة بأمان في حسابك</span>
            </footer>
        </main>
    </div>
</x-layouts.app>
