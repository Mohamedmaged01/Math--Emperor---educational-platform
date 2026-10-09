@php
    $row1 = array_merge(array_slice($testimonials, 0, 3), array_slice($testimonials, 0, 3));
    $row2 = array_merge(array_slice($testimonials, 3), array_slice($testimonials, 3));
@endphp
<section id="testimonials" class="relative overflow-hidden py-20 sm:py-28">
    <div class="absolute top-1/3 -left-52 h-[400px] w-[400px] rounded-full bg-accent-100/50 blur-[140px] dark:bg-accent-500/8" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
        <x-section-heading badge="قصص النجاح" title="درجات حقيقية —" gold-title="تتحدث عنا" :inline="true"
            desc="آلاف الطلاب دخلوا الاختبار بثقة الإمبراطور وخرجوا بدرجات فتحت لهم أبواب أفضل الجامعات والتخصصات." />
    </div>

    <div data-reveal style="--ry:30px;--rd:.9s" class="relative mt-12 space-y-4">
        <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-20 bg-gradient-to-l from-(--background) to-transparent"></div>
        <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-20 bg-gradient-to-r from-(--background) to-transparent"></div>

        @foreach ([[$row1, 'animation-duration: 46s'], [$row2, 'animation-duration: 54s; animation-direction: reverse']] as [$row, $style])
            <div class="overflow-hidden" dir="ltr">
                <div class="marquee-track flex gap-4" style="{{ $style }}">
                    @foreach ($row as $t)
                        <article class="relative w-[320px] shrink-0 rounded-2xl border border-border bg-surface p-5 shadow-sm transition-all duration-300 hover:border-primary-200 hover:shadow-md sm:w-[370px]" dir="rtl">
                            <x-i name="quote" class="absolute top-5 left-5 size-8 text-primary-100 dark:text-primary-800" />
                            <div class="flex items-center gap-3">
                                <span class="grid size-11 shrink-0 place-items-center rounded-xl border border-border bg-surface-2 text-base font-black text-primary" aria-hidden="true">{{ mb_substr((string) ($t['name'] ?? ''), 0, 1) }}</span>
                                <div class="min-w-0 flex-1">
                                    <h4 class="truncate text-small font-black text-foreground">{{ $t['name'] ?? '' }}</h4>
                                    <p class="text-[11px] font-bold text-foreground-3">{{ $t['city'] ?? '' }}</p>
                                </div>
                                <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white shadow-md">
                                    <span class="text-center leading-none">
                                        <span class="num block text-base font-black">{{ $t['score'] ?? '' }}</span>
                                        <span class="block text-[8px] font-bold opacity-85">{{ $t['exam'] ?? '' }}</span>
                                    </span>
                                </span>
                            </div>
                            <div class="mt-3 flex gap-0.5" aria-label="تقييم 5 من 5">
                                @for ($i = 0; $i < 5; $i++)
                                    <x-i name="star" class="size-3.5 fill-amber-400 text-amber-400" />
                                @endfor
                            </div>
                            <p class="mt-3 text-[13px] leading-relaxed text-foreground-2">{{ $t['text'] ?? '' }}</p>
                            @if ((int) ($t['score'] ?? 0) >= 99)
                                <x-badge tone="warning" size="sm" icon="crown" class="mt-4">نادي الـ ٩٩+</x-badge>
                            @endif
                        </article>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
