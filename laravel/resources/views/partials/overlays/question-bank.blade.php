@php
    $levels = ['الكل', 'سهل', 'متوسط', 'صعب'];
    $qbTopics = ['الكل', 'قسم كمي', 'قسم لفظي', 'الجبر', 'الهندسة', 'النسب والتناسب', 'استيعاب المقروء'];
@endphp
<div x-data="questionBank(@js(\App\Support\Content::questionBank()))" @emperor:open-question-bank.window="$store.windows.closeAll(); open = true" @emperor:close-windows.window="open = false">
    <x-window show="open" close="open = false" label="صفحة بنك الأسئلة" heading="صفحة مستقلة — بنك الأسئلة" sub="QUESTION BANK WINDOW · AI Qiyas Pro" close-label="إغلاق صفحة بنك الأسئلة">
        <main class="relative mx-auto max-w-5xl px-4 pb-20 sm:px-6">
            <div class="absolute -top-24 left-1/2 h-96 w-[700px] -translate-x-1/2 rounded-full bg-accent-100/50 blur-[130px] dark:bg-accent-500/8" aria-hidden="true"></div>

            <div class="relative pt-14 text-center sm:pt-16">
                <div data-reveal style="--ry:18px;--rd:.7s;--rdelay:.1s"><x-badge tone="accent" icon="book-open-check">بنك الأسئلة</x-badge></div>
                <h1 data-reveal style="--ry:26px;--rd:.85s;--rdelay:.2s" class="mx-auto mt-6 max-w-2xl text-display text-foreground">
                    عيّنة حية من البنك —
                    <span class="gradient-text block py-1">+١٢٬٥٠٠ سؤال مصنّف بشرح</span>
                </h1>
                <p data-reveal style="--ry:20px;--rd:.8s;--rdelay:.32s" class="mx-auto mt-5 max-w-2xl text-body-lg text-foreground-2">
                    فلتر حسب القسم والمستوى والمهارة، واقرأ شرح كل سؤال خطوة بخطوة.
                    هذه عيّنة تفاعلية من البنك الكامل.
                </p>
            </div>

            {{-- filters --}}
            <div data-reveal style="--ry:20px;--rd:.7s;--rdelay:.4s" class="relative mt-10 flex flex-col gap-3 rounded-2xl border border-border bg-surface p-4 shadow-sm sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <x-i name="search" class="pointer-events-none absolute inset-y-0 start-3.5 my-auto size-4.5 text-foreground-3" />
                    <input x-model="query" placeholder="ابحث في العيّنة… (نسب، هندسة، شُح…)" aria-label="ابحث في بنك الأسئلة"
                        class="h-11 w-full rounded-xl border border-border bg-surface-2 ps-10.5 pe-4 text-small font-bold text-foreground placeholder:text-foreground-3 focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
                </div>
                <div class="flex gap-2">
                    <div class="relative">
                        <select x-model="topic" aria-label="تصفية حسب المهارة"
                            class="h-11 appearance-none rounded-xl border border-border bg-surface-2 ps-4 pe-9 text-xs font-black text-foreground-2 focus:border-primary-400 focus:outline-none">
                            @foreach ($qbTopics as $t)
                                <option>{{ $t }}</option>
                            @endforeach
                        </select>
                        <x-i name="filter" class="pointer-events-none absolute inset-y-0 end-3 my-auto size-4 text-foreground-3" />
                    </div>
                    <div class="flex overflow-hidden rounded-xl border border-border">
                        @foreach ($levels as $l)
                            <button type="button" @click="level = '{{ $l }}'" :aria-pressed="level === '{{ $l }}'"
                                :class="level === '{{ $l }}' ? 'bg-primary-500 text-white' : 'bg-surface-2 text-foreground-2 hover:text-primary'"
                                class="px-3.5 py-2 text-xs font-black transition-colors">{{ $l }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- list --}}
            <div class="relative mt-6 space-y-4">
                <div x-show="visible.length === 0" x-cloak>
                    <x-card class="py-12 text-center">
                        <x-i name="file-question" class="mx-auto size-10 text-foreground-3" />
                        <p class="mt-3 text-subtitle text-foreground">لا نتائج في العيّنة مطابقة للفلاتر</p>
                        <p class="mt-1 text-small text-foreground-3">جرّب كلمة أو مهارة أو مستوى مختلفًا</p>
                    </x-card>
                </div>
                <template x-for="(item, i) in visible" :key="item.id">
                    <div class="anim-rise" :style="'animation-delay:' + (50 + i * 60) + 'ms'">
                        <div class="rounded-2xl border bg-surface p-5 shadow-sm transition-colors duration-300 sm:p-6" :class="openId === item.id ? 'border-primary-200 shadow-md' : 'border-border'">
                            <button type="button" @click="openId = openId === item.id ? null : item.id" :aria-expanded="openId === item.id" class="w-full text-start">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full border border-primary-100 bg-primary-soft px-2.5 py-0.5 text-[11px] font-black text-primary" x-text="item.topic"></span>
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full border border-secondary-100 bg-secondary-soft px-2.5 py-0.5 text-[11px] font-black text-secondary" x-text="item.skill"></span>
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black" :class="levelClass(item.level)" x-text="item.level"></span>
                                    <span class="mr-auto flex items-center gap-1 text-[11px] font-black text-foreground-3">
                                        <span x-text="openId === item.id ? 'إخفاء الشروح' : 'عرض الشرح'"></span>
                                        <span class="transition-transform duration-300" :class="openId === item.id && 'rotate-180'"><x-i name="chevron-down" class="size-4" /></span>
                                    </span>
                                </div>
                                <p class="mt-3 text-small font-black leading-relaxed text-foreground sm:text-base" x-text="item.q"></p>
                                <template x-if="item.eq">
                                    <span class="equation mt-2 rounded-md bg-surface-2 px-2 py-0.5 text-[0.95em] text-foreground" x-text="item.eq"></span>
                                </template>
                            </button>

                            <div class="mt-4 grid gap-2 sm:grid-cols-2">
                                <template x-for="(op, oi) in item.options" :key="oi">
                                    <div class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-small font-bold"
                                        :class="openId === item.id && oi === item.answer ? 'border-success/40 bg-success-soft text-success' : 'border-border bg-surface-2/60 text-foreground-2'">
                                        <span class="num" x-text="op"></span>
                                        <span x-show="openId === item.id && oi === item.answer"><x-i name="check-circle-2" class="size-4 text-success" /></span>
                                    </div>
                                </template>
                            </div>

                            <div x-show="openId === item.id" x-collapse.duration.400ms>
                                <div class="mt-4 flex items-start gap-2.5 rounded-xl border border-primary-100 bg-primary-soft/60 p-4">
                                    <x-i name="lightbulb" class="mt-0.5 size-4.5 shrink-0 text-primary" />
                                    <div class="rounded-xl border border-border bg-surface-2/60 p-4">
                                        <p class="text-caption text-primary">الشرح السريع</p>
                                        <ol class="mt-2 space-y-2">
                                            <li class="flex items-start gap-2.5 text-small text-foreground-2">
                                                <span class="num mt-0.5 grid size-5 shrink-0 place-items-center rounded-full bg-primary-soft text-[10px] font-black text-primary">1</span>
                                                <span class="min-w-0" x-text="item.explain"></span>
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <template x-for="tag in item.tags" :key="tag">
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full border border-border bg-surface-2 px-2.5 py-0.5 text-[11px] font-black text-foreground-2" x-text="'#' + tag"></span>
                                    </template>
                                    <a href="#pricing" @click="open = false" class="mr-auto inline-flex items-center gap-1.5 text-[11px] font-black text-primary transition-colors hover:text-primary-hover">
                                        <x-i name="book-open-check" class="size-3.5" />
                                        +١٢٬٥٠٠ سؤال داخل الباقات
                                        <x-i name="chevron-left" class="size-3.5" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <p class="mt-10 text-center text-caption text-foreground-3">
                اضغط <kbd class="num rounded-md border border-border bg-surface px-1.5 py-0.5 text-[10px] font-black">ESC</kbd> أو زر الإغلاق للعودة إلى الصفحة الرئيسية.
            </p>
        </main>
    </x-window>
</div>
