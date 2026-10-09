@php
    $sevTone = ['حرج' => 'danger', 'تحذيري' => 'warning', 'إيجابي' => 'success'];
    $kpiCards = [
        ['طلاب نشطون', $kpi['activeStudents'], 'text-success'], ['حسابات معلقة', $kpi['suspendedStudents'], 'text-warning'],
        ['أسئلة منشورة', $kpi['publishedQuestions'], 'text-primary'], ['بانتظار المراجعة', $kpi['pendingQuestions'], 'text-danger'],
        ['محاولات المحاكي', $kpi['totalExamAttempts'], 'text-secondary'], ['إيراد (ر.س)', number_format($kpi['revenueSar']), 'text-accent'],
        ['طلبات مدفوعة', $kpi['ordersPaid'], 'text-success'], ['تنزيلات الملفات', $kpi['downloads'], 'text-primary'],
    ];

    /* ---- area chart geometry ---- */
    $w = 560; $h = 150; $pad = 8;
    $max = max(array_column($series, 'value') ?: [1]);
    $n = count($series);
    $pts = [];
    foreach ($series as $i => $d) {
        $pts[] = [$pad + ($i / max(1, $n - 1)) * ($w - $pad * 2), $h - $pad - ($d['value'] / $max) * ($h - $pad * 2)];
    }
    $line = collect($pts)->map(fn ($p, $i) => ($i === 0 ? 'M' : 'L').number_format($p[0], 1, '.', '').','.number_format($p[1], 1, '.', ''))->implode(' ');
    $area = $line.' L'.number_format(end($pts)[0], 1, '.', '').','.($h - $pad).' L'.number_format($pts[0][0], 1, '.', '').','.($h - $pad).' Z';

    /* ---- donut ---- */
    $total = array_sum(array_column($plans, 'value')) ?: 1;
    $donutColors = ['var(--color-accent-500)', '#e6b636', '#e6b636', 'var(--color-primary-500)'];
    $C = 2 * M_PI * 40;

    $funnelMax = max(array_column($funnel, 'value') ?: [1]) ?: 1;
    $funnelTones = ['var(--color-primary-500)', 'var(--color-secondary-500)', 'var(--color-accent-500)', '#e6b636'];
    $examMax = max(array_column($examBars, 'value') ?: [1]) ?: 1;
@endphp
<div x-data="{ scope: 'all' }" class="space-y-6">
    {{-- executive summary --}}
    <x-card class="border-primary-100 bg-primary-soft/50">
        <div class="flex flex-wrap items-start gap-4">
            <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-surface text-primary shadow-xs"><x-i name="brain" class="size-6" /></span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-subtitle text-foreground">الملخص التنفيذي الذكي</h3>
                    <x-badge tone="secondary" size="sm">AI Analytics Engine</x-badge>
                    <x-badge size="sm">مُحلَّل بواسطة: {{ $user->name }}</x-badge>
                </div>
                <p class="mt-2 text-small font-bold leading-relaxed text-foreground-2">{{ $summary }}</p>
            </div>
        </div>
    </x-card>

    {{-- KPI strip --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach ($kpiCards as $i => [$label, $value, $tone])
            <div class="anim-rise" style="animation-delay: {{ $i * 50 }}ms">
                <x-card class="p-4!">
                    <p class="num text-xl font-black sm:text-2xl {{ $tone }}">{{ $value }}</p>
                    <p class="mt-1 text-[10px] font-black text-foreground-3">{{ $label }}</p>
                </x-card>
            </div>
        @endforeach
    </div>

    {{-- charts row 1 --}}
    <div class="grid gap-5 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-subtitle">نشاط المنصة — ١٤ يومًا</h3>
                <x-badge tone="primary" size="sm">محاولات محاكاة</x-badge>
            </div>
            <svg viewBox="0 0 {{ $w }} {{ $h }}" class="w-full" role="img" aria-label="رسم بياني لنشاط المنصة خلال ١٤ يومًا">
                <defs>
                    <linearGradient id="actGrad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="var(--color-primary-500)" stop-opacity="0.35" />
                        <stop offset="100%" stop-color="var(--color-primary-500)" stop-opacity="0.02" />
                    </linearGradient>
                    <linearGradient id="lineGrad" x1="0" y1="0" x2="1" y2="0">
                        <stop offset="0%" stop-color="var(--color-secondary-500)" />
                        <stop offset="100%" stop-color="var(--color-primary-500)" />
                    </linearGradient>
                </defs>
                @foreach ([0.25, 0.5, 0.75] as $f)
                    <line x1="{{ $pad }}" x2="{{ $w - $pad }}" y1="{{ $h * $f }}" y2="{{ $h * $f }}" stroke="var(--color-border)" stroke-dasharray="3 5" stroke-width="1" />
                @endforeach
                <path data-reveal="fade" style="--rd:1s" d="{{ $area }}" fill="url(#actGrad)" />
                <path data-draw d="{{ $line }}" pathLength="1" fill="none" stroke="url(#lineGrad)" stroke-width="2.5" stroke-linecap="round" />
                @foreach ($pts as $i => $p)
                    <circle data-pop style="--rdelay: {{ 0.6 + $i * 0.04 }}s" cx="{{ $p[0] }}" cy="{{ $p[1] }}" r="{{ $i === $n - 1 ? 5 : 3 }}"
                        fill="{{ $i === $n - 1 ? 'var(--color-secondary-500)' : 'var(--color-primary-300)' }}" stroke="var(--color-surface)" stroke-width="1.5" />
                @endforeach
            </svg>
            <div class="mt-2 flex justify-between px-1 text-[9px] font-black text-foreground-3" dir="ltr">
                <span>{{ $series[0]['label'] }}</span>
                <span>{{ $series[intdiv($n, 2)]['label'] }}</span>
                <span class="text-secondary">{{ $series[$n - 1]['label'] }} · {{ $series[$n - 1]['value'] }}</span>
            </div>
        </x-card>
        <x-card>
            <h3 class="text-subtitle">توزيع الباقات</h3>
            <div class="mt-5 flex items-center gap-5">
                <div class="relative size-36 shrink-0">
                    <svg viewBox="0 0 100 100" class="size-36 -rotate-90" role="img" aria-label="توزيع الباقات">
                        <circle cx="50" cy="50" r="40" fill="none" stroke="var(--color-surface-3)" stroke-width="13" />
                        @php $offset = 0; @endphp
                        @foreach ($plans as $i => $d)
                            @php $frac = $d['value'] / $total; @endphp
                            <circle data-ring style="--from: {{ $C }}; --to: {{ $C - $offset - $C * $frac }}; --rd: 1.1s; --rdelay: {{ 0.15 * $i }}s"
                                cx="50" cy="50" r="40" fill="none" stroke="{{ $donutColors[$i % 4] }}" stroke-width="13" stroke-linecap="round" stroke-dasharray="{{ $C }}" />
                            @php $offset += $C * $frac; @endphp
                        @endforeach
                    </svg>
                    <div class="absolute inset-0 grid place-items-center">
                        <div class="text-center">
                            <p class="num text-2xl font-black text-foreground">{{ array_sum(array_column($plans, 'value')) }}</p>
                            <p class="text-[9px] font-black text-foreground-3">طالب</p>
                        </div>
                    </div>
                </div>
                <ul class="min-w-0 flex-1 space-y-2.5">
                    @foreach ($plans as $i => $d)
                        <li class="flex items-center gap-2.5">
                            <span class="size-3 shrink-0 rounded-full" style="background: {{ $donutColors[$i % 4] }}"></span>
                            <span class="min-w-0 flex-1 truncate text-xs font-black text-foreground-2">{{ $d['label'] }}</span>
                            <span class="num text-xs font-black text-foreground">{{ $d['value'] }}</span>
                            <span class="num text-[10px] font-bold text-foreground-3">{{ round($d['value'] / $total * 100) }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </x-card>
    </div>

    {{-- charts row 2 --}}
    <div class="grid gap-5 lg:grid-cols-3">
        <x-card>
            <h3 class="text-subtitle">تغطية المهارات</h3>
            <p class="mb-3 text-[10px] font-bold text-foreground-3">منشور / إجمالي لكل مهارة</p>
            <div class="space-y-2.5">
                @foreach ($heat as $i => $row)
                    @php $pct = (int) round($row['published'] / max(1, $row['total']) * 100); @endphp
                    <div data-reveal style="--rx:18px;--ry:0px;--rd:.45s;--rdelay:{{ $i * 0.06 }}s" class="flex items-center gap-3">
                        <span class="w-32 shrink-0 truncate text-[11px] font-black text-foreground-2">{{ $row['skill'] }}</span>
                        <div class="h-6 flex-1 overflow-hidden rounded-lg bg-surface-3">
                            <div data-bar style="--w: {{ $pct }}%; --rd: .8s; --rdelay: {{ 0.15 + $i * 0.06 }}s"
                                @class(['flex h-full items-center justify-end rounded-lg pe-2', 'bg-gradient-to-l from-success-500 to-success-600' => $pct >= 80, 'bg-gradient-to-l from-amber-400 to-amber-500' => $pct >= 50 && $pct < 80, 'bg-gradient-to-l from-rose-400 to-rose-500' => $pct < 50])>
                                <span class="num text-[10px] font-black text-white/95">{{ $pct }}%</span>
                            </div>
                        </div>
                        <span class="num w-16 shrink-0 text-[10px] font-bold text-foreground-3" dir="ltr">{{ $row['published'] }}/{{ $row['total'] }}</span>
                    </div>
                @endforeach
            </div>
        </x-card>
        <x-card>
            <h3 class="text-subtitle">قمع التحويل</h3>
            <p class="mb-3 text-[10px] font-bold text-foreground-3">زائر ← مسجل ← دافع</p>
            <div class="space-y-3">
                @foreach ($funnel as $i => $d)
                    @php
                        $pct = (int) round($d['value'] / $funnelMax * 100);
                        $conv = $i > 0 ? (int) round($d['value'] / max(1, $funnel[$i - 1]['value']) * 100) : 100;
                        $tone = $funnelTones[$i % 4];
                    @endphp
                    <div data-reveal style="--ry:12px;--rd:.5s;--rdelay:{{ $i * 0.1 }}s">
                        <div class="mb-1 flex items-center justify-between text-[11px] font-black">
                            <span class="text-foreground-2">{{ $d['label'] }}</span>
                            <span class="num text-foreground-3">{{ $d['value'] }} · تحويل {{ $conv }}%</span>
                        </div>
                        <div class="h-9 overflow-hidden rounded-xl bg-surface-3">
                            <div data-bar class="flex h-full items-center justify-between rounded-xl px-3"
                                style="--w: {{ $pct }}%; --rdelay: {{ 0.2 + $i * 0.1 }}s; background: linear-gradient(to left, {{ $tone }}, color-mix(in srgb, {{ $tone }} 80%, transparent))">
                                <span class="num text-xs font-black text-white">{{ number_format($d['value']) }}</span>
                                <span class="num text-[10px] font-black text-white/70">{{ $pct }}%</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
        <x-card>
            <h3 class="text-subtitle">المحاولات لكل نموذج</h3>
            @if (count($examBars))
                <div class="mt-5 flex h-40 items-end gap-2.5" dir="ltr" role="img" aria-label="المحاولات لكل نموذج">
                    @foreach ($examBars as $i => $d)
                        <div class="group flex h-full flex-1 flex-col items-center justify-end gap-2">
                            <span class="num text-[10px] font-black text-foreground-3 opacity-0 transition-opacity group-hover:opacity-100">{{ $d['value'] }}</span>
                            <div data-hbar style="--h: {{ $d['value'] / $examMax * 100 }}%; --rdelay: {{ $i * 0.09 }}s" title="{{ $d['label'] }}: {{ $d['value'] }}"
                                @class(['w-full rounded-t-lg transition-all', 'bg-gradient-to-t from-secondary-600 to-secondary-400 shadow-lg shadow-secondary-500/25' => $i === count($examBars) - 1, 'bg-gradient-to-t from-primary-700 to-primary-400' => $i !== count($examBars) - 1])></div>
                            <span class="w-full truncate text-center text-[9px] font-black text-foreground-3" dir="rtl">{{ $d['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="mt-8 text-center text-xs font-bold text-foreground-3">لا محاولات مسجلة بعد</p>
            @endif
        </x-card>
    </div>

    {{-- AI insights --}}
    <x-card :padded="false">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-border px-5 py-4">
            <div class="flex items-center gap-2.5">
                <span class="grid size-10 place-items-center rounded-xl bg-accent-soft text-accent"><x-i name="sparkles" class="size-5" /></span>
                <div>
                    <h3 class="text-subtitle">توصيات القرار — محرك التحليل</h3>
                    <p class="text-[10px] font-bold text-foreground-3">قواعد حتمية على مؤشرات حقيقية · كل توصية قابلة للتتبع لمصدرها</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-1.5">
                @foreach (['all' => 'الكل ('.count($insights).')', 'نمو' => 'نمو', 'إيراد' => 'إيراد', 'تعلم' => 'تعلم', 'تشغيل' => 'تشغيل', 'مخاطرة' => 'مخاطرة'] as $s => $label)
                    <button type="button" @click="scope = @js($s)" :aria-pressed="scope === @js($s)"
                        :class="scope === @js($s) ? 'bg-gradient-to-l from-accent-500 to-accent-600 text-white shadow-md shadow-accent-500/25' : 'border border-border bg-surface text-foreground-2 hover:text-accent'"
                        class="rounded-full px-3.5 py-1.5 text-[11px] font-black transition-all">{{ $label }}</button>
                @endforeach
            </div>
        </div>
        <ul class="divide-y divide-border">
            @foreach ($insights as $i => $ins)
                <li x-show="scope === 'all' || scope === @js($ins['scope'])" data-reveal style="--rx:20px;--ry:0px;--rd:.45s;--rdelay:{{ $i * 0.06 }}s" class="flex flex-wrap items-start gap-4 px-5 py-4">
                    <x-badge :tone="$sevTone[$ins['severity']] ?? 'neutral'" size="sm" class="mt-0.5 shrink-0">{{ $ins['severity'] }}</x-badge>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="text-small font-black text-foreground">{{ $ins['title'] }}</h4>
                            @if (! empty($ins['metric']))
                                <span class="num rounded-md bg-surface-2 px-2 py-0.5 text-[10px] font-black text-primary">{{ $ins['metric'] }}</span>
                            @endif
                            <x-badge size="sm">{{ $ins['horizon'] }}</x-badge>
                            <x-badge tone="secondary" size="sm">{{ $ins['scope'] }}</x-badge>
                        </div>
                        <p class="mt-1 text-xs font-bold leading-relaxed text-foreground-2">{{ $ins['detail'] }}</p>
                        <p class="mt-1.5 flex items-start gap-1.5 rounded-lg border border-accent-100 bg-accent-soft/50 px-3 py-2 text-xs font-bold leading-relaxed text-foreground-2">
                            <x-i name="zap" class="mt-0.5 size-3.5 shrink-0 text-accent" />
                            <span><span class="font-black text-accent">القرار المقترح: </span>{{ $ins['recommendation'] }}</span>
                        </p>
                    </div>
                </li>
            @endforeach
        </ul>
        @if ($canExport)
            <div class="border-t border-border px-5 py-4">
                <x-button size="sm" variant="outline" :href="url('/imperial/api/records/audit/export?module=analytics&format=csv')">تصدير تقرير التحليل (CSV)</x-button>
            </div>
        @endif
    </x-card>
</div>
