@php $quick = ['معسكر الإمبراطور', 'محاكي قياس', 'حاسبة الموزونة', 'المسار المتقدم', 'خطة الانطلاق']; @endphp
<div x-data="siteSearch(@js(\App\Support\Content::searchIndex()))" @emperor:open-search.window="show()">
    <template x-teleport="body">
        <div x-show="open" x-cloak x-effect="$store.lock.set('site-search', open)" @keydown.escape.window="open = false"
            x-transition:leave="duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-100">
            <div x-show="open" x-transition.opacity @click="open = false" class="fixed inset-0 z-[78] bg-foreground/45 backdrop-blur-sm" aria-hidden="true"></div>
            <div x-show="open" role="dialog" aria-modal="true" aria-label="البحث في المنصة"
                x-transition:enter="transition duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="opacity-0 -translate-y-5 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-3.5 scale-[0.98]"
                class="fixed inset-x-0 top-[10%] z-[80] mx-auto flex max-h-[76vh] w-[min(640px,92vw)] flex-col overflow-hidden rounded-3xl border border-border bg-surface shadow-2xl">
                {{-- input --}}
                <div class="flex items-center gap-2.5 border-b border-border px-5 py-4">
                    <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-primary-soft text-primary">
                        <x-i name="search" class="size-5" />
                    </span>
                    <input x-ref="input" x-model="q" @keydown.enter="hits.length && pick(hits[0])"
                        placeholder="ابحث: دورة، أداة، باقة، قسم، بوابة…" aria-label="ابحث في المنصة"
                        class="h-9 flex-1 bg-transparent text-base font-bold text-foreground placeholder:text-foreground-3 focus:outline-none" />
                    <button type="button" @click="open = false" aria-label="إغلاق البحث"
                        class="grid size-9 place-items-center rounded-xl text-foreground-3 transition-colors hover:bg-surface-2 hover:text-foreground">
                        <x-i name="x" class="size-4.5" />
                    </button>
                </div>

                {{-- results / empty states --}}
                <div class="flex-1 overflow-y-auto p-3">
                    <div x-show="q.trim() === ''" class="p-4">
                        <p class="text-xs font-black text-foreground-3">اقتراحات شائعة:</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($quick as $s)
                                <button type="button" @click="q = @js($s)"
                                    class="rounded-full border border-primary-200 bg-primary-soft/70 px-3.5 py-2 text-[11px] font-black text-primary transition-colors hover:bg-primary-100">{{ $s }}</button>
                            @endforeach
                        </div>
                        <p class="mt-6 flex items-center gap-2 text-[10px] font-bold text-foreground-3">
                            <x-i name="command" class="size-3.5" />
                            اختر نتيجة بالضغط أو أول نتيجة بزر Enter عند الكتابة — ESC للإغلاق
                        </p>
                    </div>

                    <div x-show="q.trim() !== '' && hits.length === 0" x-cloak class="flex flex-col items-center py-14 text-center">
                        <x-i name="file-question" class="size-10 text-foreground-3" />
                        <p class="mt-3 text-subtitle text-foreground" x-text="'لا نتائج عن «' + q.trim() + '»'"></p>
                        <p class="mt-1 max-w-xs text-small font-bold text-foreground-3">جرّب اسم دورة أو أداة أو باقة — أو استخدم أحد الاقتراحات بالأعلى.</p>
                    </div>

                    <ul x-show="hits.length > 0" x-cloak class="space-y-1">
                        <template x-for="(h, i) in hits" :key="h.id">
                            <li class="anim-rise" :style="'animation-delay:' + (i * 30) + 'ms'">
                                <button type="button" @click="pick(h)"
                                    :class="i === 0 ? 'bg-primary-soft/60 ring-1 ring-primary-200 hover:bg-primary-soft' : 'hover:bg-surface-2'"
                                    class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3 text-start transition-colors">
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black" :class="kindClass(h.kind)" x-text="h.kind"></span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-small font-black text-foreground" x-text="h.title"></p>
                                        <p class="truncate text-[10px] font-bold text-foreground-3" x-text="h.sub"></p>
                                    </div>
                                    <x-i name="arrow-up-left" class="size-4 shrink-0 text-foreground-3" />
                                </button>
                            </li>
                        </template>
                    </ul>
                </div>

                <div class="border-t border-border bg-surface-2/60 px-5 py-2.5 text-center text-[10px] font-bold text-foreground-3">
                    البحث يعثر داخل المنصة فقط — نتائج وتحدياتك الخاصة موجودة داخل بوابتك كطالب.
                </div>
            </div>
        </div>
    </template>
</div>
