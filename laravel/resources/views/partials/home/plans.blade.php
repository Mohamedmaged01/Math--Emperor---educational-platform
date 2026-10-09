@php
    use App\Support\Content;
    $plans = Content::studyPlans();
    $week = Content::weekSchedule();
    $planStyles = ['from-secondary-500 to-secondary-600', 'from-primary-400 to-primary-700', 'from-accent-500 to-accent-600'];
    $typeIcon = ['تعلم' => 'book-marked', 'تدريب' => 'dumbbell', 'محاكاة' => 'clock-3', 'مراجعة' => 'refresh-ccw'];
@endphp
<section id="plans" x-data="{ active: 1 }" class="relative overflow-hidden py-20 sm:py-28">
    <div class="absolute bottom-0 -right-52 h-[420px] w-[420px] rounded-full bg-secondary-100/60 blur-[140px] dark:bg-secondary-500/6" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
        <x-section-heading badge="الخطط الذكية" title="خطة مبنية عليك —" :inline="true" gold-title="تتكيف معك يوميًا"
            desc="اختر إيقاعك، والمنصة تعيد ترتيب أسبوعك تلقائيًا بناءً على أدائك في المحاكيات والتدريبات." />

        <div class="mt-14 grid items-start gap-8 lg:grid-cols-2">
            {{-- ------- plan cards ------- --}}
            <div class="space-y-3.5">
                @foreach ($plans as $i => $plan)
                    <div data-reveal style="--rx:40px;--ry:0px;--rd:.7s;--rdelay:{{ $i * 0.12 }}s">
                        <button type="button" @click="active = {{ $i }}" :aria-pressed="active === {{ $i }}"
                            :class="active === {{ $i }} ? 'border-primary-300 bg-primary-soft/50 shadow-lg shadow-primary-500/10' : 'border-border bg-surface shadow-sm hover:border-border-strong hover:shadow-md'"
                            class="relative w-full overflow-hidden rounded-2xl border p-5 text-right transition-all duration-300 sm:p-6">
                            @if ($plan['featured'])
                                <x-badge tone="primary" size="sm" class="absolute top-4 left-4">الأكثر اختيارًا</x-badge>
                            @endif
                            <div class="flex items-center gap-4">
                                <span :class="active === {{ $i }} && 'scale-105'"
                                    class="grid size-13 shrink-0 place-items-center rounded-2xl bg-gradient-to-br text-white shadow-md transition-transform duration-300 {{ $planStyles[$i] }}">
                                    <x-i name="route" class="size-6.5" stroke-width="2.2" />
                                </span>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2.5">
                                        <h3 class="text-subtitle text-foreground">{{ $plan['name'] }}</h3>
                                        <span class="num rounded-full bg-surface-2 px-2.5 py-0.5 text-[10px] font-black text-foreground-2">{{ $plan['duration'] }}</span>
                                    </div>
                                    <p class="mt-0.5 text-xs font-bold text-foreground-3">{{ $plan['level'] }}</p>
                                </div>
                                <span :class="active === {{ $i }} ? 'border-primary-500 bg-primary-500' : 'border-border-strong bg-surface'"
                                    class="grid size-6 shrink-0 place-items-center rounded-full border-2 transition-all duration-300" aria-hidden="true">
                                    <span x-show="active === {{ $i }}"><x-i name="check-circle-2" class="size-4 text-white" stroke-width="3" /></span>
                                </span>
                            </div>

                            <div x-show="active === {{ $i }}" x-collapse.duration.450ms @if ($i !== 1) x-cloak @endif>
                                <div class="mt-5 grid gap-2 border-t border-border pt-5 sm:grid-cols-3">
                                    @foreach ($plan['weekly'] as $w)
                                        <span class="flex items-center gap-1.5 text-[11px] font-bold text-foreground-2">
                                            <x-i name="calendar-range" class="size-3.5 text-primary" />
                                            {{ $w }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($plan['milestones'] as $m)
                                        <x-badge tone="primary" size="sm">{{ $m }}</x-badge>
                                    @endforeach
                                </div>
                            </div>
                        </button>
                    </div>
                @endforeach

                <a href="#pricing" data-reveal style="--ry:20px;--rd:.7s;--rdelay:.35s"
                    class="group flex items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-8 py-4 text-small font-black text-white shadow-lg shadow-primary-500/25 transition-all duration-200 hover:brightness-110 active:scale-[0.99]">
                    أنشئ خطتك الذكية الآن
                    <x-i name="chevron-left" class="size-4.5 transition-transform duration-300 group-hover:-translate-x-1" />
                </a>
            </div>

            {{-- ------- week preview ------- --}}
            <div data-reveal style="--ry:40px;--rd:.9s;--rdelay:.15s" class="relative overflow-hidden rounded-3xl border border-border bg-surface p-5 shadow-xl sm:p-7">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-subtitle text-foreground">أسبوعك القادم</h3>
                        <p class="text-xs font-bold text-foreground-3">الأسبوع ٥ — خطة الانطلاق</p>
                    </div>
                    <x-badge tone="success" size="sm" icon="check-circle-2"><span class="num">43%</span> مكتمل</x-badge>
                </div>

                <div class="mt-5 space-y-2">
                    @foreach ($week as $i => $d)
                        <div data-reveal style="--rx:24px;--ry:0px;--rd:.5s;--rdelay:{{ 0.2 + $i * 0.08 }}s"
                            @class(['flex items-center gap-3.5 rounded-xl border px-3.5 py-2.5 transition-colors duration-300', 'border-primary-300 bg-primary-soft/60' => $d['active'], 'border-border bg-surface-2/50' => ! $d['active']])>
                            <span @class(['grid size-10 shrink-0 place-items-center rounded-lg border text-[11px] font-black', 'border-primary-200 bg-surface text-primary' => $d['active'], 'border-border bg-surface text-foreground-2' => ! $d['active']])>{{ $d['day'] }}</span>
                            <div class="min-w-0 flex-1">
                                <p @class(['truncate text-small font-black', 'text-foreground-3 line-through decoration-border-strong' => $d['done'], 'text-foreground' => ! $d['done']])>{{ $d['title'] }}</p>
                                <p class="text-[10px] font-bold text-foreground-3"><span class="num">{{ $d['mins'] }}</span> دقيقة · {{ $d['type'] }}</p>
                            </div>
                            @if ($d['done'])
                                <x-i name="check-circle-2" class="size-5 shrink-0 text-success" aria-label="مكتمل" />
                            @elseif ($d['active'])
                                <span class="relative flex size-3 shrink-0" aria-label="جارٍ الآن">
                                    <span class="absolute inline-flex size-full animate-ping rounded-full bg-primary-400 opacity-70"></span>
                                    <span class="relative inline-flex size-3 rounded-full bg-primary-500"></span>
                                </span>
                            @else
                                <x-i :name="$typeIcon[$d['type']] ?? 'book-marked'" class="size-4.5 shrink-0 text-foreground-3" />
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 rounded-xl border border-secondary-100 bg-secondary-soft p-4">
                    <p class="text-[11px] font-bold leading-relaxed text-foreground-2">
                        <span class="font-black text-secondary">تعديل تلقائي:</span>
                        رصد النظام ضعفًا في «الهندسة» فأضاف جلسة تقوية الخميس وقلّص جلسة اللفظي.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
