@php
    $courses = \App\Support\Content::courses();
    $trackTabs = [['key' => 'all', 'label' => 'جميع الدورات'], ['key' => 'qud', 'label' => 'دورات القدرات العامة'], ['key' => 'tah', 'label' => 'دورات التحصيلي العلمي']];
@endphp
<section id="courses" x-data="{ track: 'all', open: false, selected: null, courses: @js($courses) }" class="relative py-20 sm:py-28">
    <div class="absolute top-1/3 -left-48 h-[420px] w-[420px] rounded-full bg-primary-100/60 blur-[140px] dark:bg-primary-500/8" aria-hidden="true"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
        <x-section-heading
            badge="منهاج التأسيس والاحتراف لاختبارات قياس"
            title="دورات وشروحات الإمبراطور التأسيسية"
            gold-title=""
            desc="شروحات تفكك المفاهيم المعقدة من الصفر، مع ملخصات PDF مجانية وتطبيقات عملية فورية لكل فكرة." />

        {{-- tabs --}}
        <div class="mt-10 flex flex-wrap justify-center gap-2" role="tablist" aria-label="تصنيف الدورات">
            @foreach ($trackTabs as $t)
                <button type="button" role="tab" :aria-selected="track === '{{ $t['key'] }}'" @click="track = '{{ $t['key'] }}'"
                    :class="track === '{{ $t['key'] }}'
                        ? 'bg-gradient-to-l from-primary-500 to-primary-700 text-white shadow-md shadow-primary-500/25'
                        : 'border border-border bg-surface text-foreground-2 hover:border-primary-200 hover:text-primary'"
                    class="rounded-full px-5 py-2.5 text-small font-black transition-all duration-200">{{ $t['label'] }}</button>
            @endforeach
        </div>

        {{-- catalog --}}
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($courses as $i => $c)
                <article x-show="track === 'all' || track === '{{ $c['track'] }}'"
                    x-transition:enter="transition duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-[0.96]">
                    <div data-reveal style="--ry:32px;--rd:.6s;--rdelay:{{ ($i % 3) * 0.08 }}s" class="h-full">
                        <x-card interactive :padded="false" class="group flex h-full flex-col overflow-hidden">
                            {{-- photo header --}}
                            <div class="relative h-44 overflow-hidden sm:h-48">
                                <img src="{{ $c['image'] }}" alt="{{ $c['imgAlt'] }}" loading="lazy"
                                    class="size-full object-cover object-[50%_28%] transition-transform duration-[1.2s] ease-out group-hover:scale-[1.06]" />
                                <div class="absolute inset-0 bg-gradient-to-t from-foreground/60 via-foreground/10 to-transparent" aria-hidden="true"></div>
                                <div class="absolute inset-x-0 bottom-0 h-2/3 bg-gradient-to-t opacity-70 mix-blend-multiply {{ $c['accent'] }}" aria-hidden="true"></div>
                                <div class="absolute inset-x-4 top-3.5 flex items-start justify-between gap-2">
                                    <x-badge tone="neutral" size="sm" class="border-white/25 bg-white/15 text-white backdrop-blur-sm">{{ $c['badge'] }}</x-badge>
                                    @if ($c['top'])
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-300/95 px-2.5 py-1 text-[10px] font-black text-amber-950 shadow-md">
                                            <x-i name="flame" class="size-3" />
                                            الأقوى
                                        </span>
                                    @endif
                                </div>
                                <div class="absolute bottom-3 start-4 flex items-center gap-3 text-white">
                                    <span class="num inline-flex items-center gap-1.5 rounded-full bg-black/25 px-3 py-1 text-[11px] font-black backdrop-blur-sm">
                                        <x-i name="clock-3" class="size-3.5" />
                                        {{ $c['hours'] }} ساعة تدريبية
                                    </span>
                                    <span class="num inline-flex items-center gap-1 rounded-full bg-black/25 px-3 py-1 text-[11px] font-black backdrop-blur-sm">
                                        <x-i name="star" class="size-3.5 fill-amber-300 text-amber-300" />
                                        {{ $c['rating'] }}
                                    </span>
                                </div>
                            </div>

                            {{-- body --}}
                            <div class="flex flex-1 flex-col p-5">
                                <h3 class="text-subtitle leading-snug text-foreground">{{ $c['title'] }}</h3>
                                <p class="mt-1.5 line-clamp-2 text-xs leading-relaxed text-foreground-2">{{ $c['desc'] }}</p>

                                <div class="mt-4 flex items-center gap-3 rounded-xl border border-border bg-surface-2/60 px-3.5 py-2.5">
                                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-primary-400 to-primary-700 text-sm font-black text-white">د</span>
                                    <div class="min-w-0 leading-tight">
                                        <p class="truncate text-xs font-black text-foreground">{{ $c['instructor'] }}</p>
                                        <p class="truncate text-[10px] font-bold text-foreground-3">{{ $c['instructorRole'] }}</p>
                                    </div>
                                    <x-i name="user-round-check" class="ml-auto size-4.5 shrink-0 text-secondary" />
                                </div>

                                <div class="mt-3 flex items-center justify-between text-[11px] font-black text-foreground-3">
                                    <span class="inline-flex items-center gap-1"><x-i name="book-open-check" class="size-3.5 text-primary" />{{ $c['lessons'] }} درسًا تفاعليًا</span>
                                    <span class="inline-flex items-center gap-1"><x-i name="users" class="size-3.5 text-primary" />{{ $c['students'] }} طالبًا</span>
                                </div>

                                <button type="button" @click="selected = courses[{{ $i }}]; open = true"
                                    class="group/btn mt-4 flex items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-4 py-3 text-small font-black text-white shadow-md shadow-primary-500/25 transition-all duration-200 hover:brightness-110">
                                    <x-i name="play-circle" class="size-4.5" />
                                    عرض محتوى الدورة
                                    <span class="transition-transform duration-300 group-hover/btn:-translate-x-1">←</span>
                                </button>
                            </div>
                        </x-card>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    {{-- course details + free lessons modal --}}
    <x-modal show="open" close="open = false" size="lg">
        <template x-if="selected">
            <div class="anim-rise">
                <div class="relative -mx-6 -mt-6 mb-5 h-40 overflow-hidden rounded-t-2xl sm:-mx-7 sm:-mt-7">
                    <img :src="selected.image" :alt="selected.imgAlt" class="size-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-foreground/75 to-transparent"></div>
                    <div class="absolute inset-x-6 bottom-3.5 flex items-end justify-between gap-3 sm:inset-x-7">
                        <div>
                            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-white/25 bg-white/15 px-2.5 py-0.5 text-[11px] font-black text-white backdrop-blur-sm" x-text="selected.badge"></span>
                            <h3 class="mt-2 text-title leading-snug text-white" x-text="selected.title"></h3>
                        </div>
                    </div>
                </div>

                <p class="text-small leading-relaxed text-foreground-2" x-text="selected.desc"></p>

                <div class="mt-4 flex items-center gap-3 rounded-xl border border-border bg-surface-2/60 px-4 py-3">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-primary-400 to-primary-700 text-sm font-black text-white">د</span>
                    <div class="leading-tight">
                        <p class="text-xs font-black text-foreground" x-text="selected.instructor"></p>
                        <p class="text-[10px] font-bold text-foreground-3" x-text="selected.instructorRole"></p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-4 gap-2 text-center">
                    <template x-for="m in [{ v: selected.lessons, l: 'درسًا' }, { v: selected.hours, l: 'ساعة' }, { v: selected.students, l: 'طالبًا' }, { v: selected.rating, l: 'التقييم' }]" :key="m.l">
                        <div class="rounded-xl bg-surface-2 px-2 py-3">
                            <p class="num text-base font-black text-foreground" x-text="m.v"></p>
                            <p class="text-[10px] font-bold text-foreground-3" x-text="m.l"></p>
                        </div>
                    </template>
                </div>

                <div class="mt-5">
                    <h4 class="text-small font-black text-foreground">الدروس المجانية — جرّب قبل الاشتراك</h4>
                    <ul class="mt-2.5 space-y-2">
                        <template x-for="(l, i) in selected.freePreview" :key="i">
                            <li class="flex items-center gap-3 rounded-xl border border-border bg-surface-2/60 px-4 py-3">
                                <span class="num grid size-8 shrink-0 place-items-center rounded-lg bg-primary-soft text-[11px] font-black text-primary" dir="ltr" x-text="String(i + 1).padStart(2, '0')"></span>
                                <p class="flex-1 text-xs font-black text-foreground" x-text="l.title"></p>
                                <span class="num inline-flex items-center gap-1 text-[10px] font-black text-foreground-3">
                                    <x-i name="clock-3" class="size-3.5" />
                                    <span x-text="l.mins + ' د'"></span>
                                </span>
                                <span class="inline-flex items-center gap-1 rounded-full bg-success-soft px-2.5 py-1 text-[10px] font-black text-success">
                                    <x-i name="layers" class="size-3" /> PDF مجاني
                                </span>
                            </li>
                        </template>
                    </ul>
                </div>

                <a href="#pricing" @click="open = false"
                    class="mt-5 flex items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-5 py-3.5 text-small font-black text-white shadow-lg shadow-primary-500/25 transition-all hover:brightness-110"
                    x-text="'اشترك وافتح باقي ' + (selected.lessons - selected.freePreview.length) + ' درسًا'"></a>
            </div>
        </template>
    </x-modal>
</section>
