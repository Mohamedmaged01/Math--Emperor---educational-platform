@php
    $seoCount = count($pages);
    $noindexCount = count(array_filter($pages, fn ($p) => $p['robots'] === 'noindex,nofollow'));
    $inp = 'h-11 w-full rounded-xl border border-border bg-surface-2 px-3.5 text-small font-black text-foreground focus:border-primary-400 focus:outline-none';
@endphp
<div x-data="seoPage(@js($pages), @js($overrides))" class="space-y-6">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        @foreach ([[$seoCount, 'صفحات قابلة للفهرسة'], ['٨/٨', 'Meta + OG + Schema'], [$noindexCount, 'صفحات noindex'], [\App\Support\Seo::DOMAIN, 'Display Domain (canonical)']] as [$v, $l])
            <x-card class="p-4! text-center"><p class="num text-xl font-black text-foreground">{{ $v }}</p><p class="mt-1 text-[10px] font-black text-foreground-3">{{ $l }}</p></x-card>
        @endforeach
    </div>

    <div class="grid gap-5 lg:grid-cols-5">
        <x-card class="lg:col-span-3">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="grid size-11 place-items-center rounded-2xl bg-primary-soft text-primary"><x-i name="tag" class="size-5.5" /></span>
                    <div>
                        <h3 class="text-subtitle">محرر SEO لكل صفحة</h3>
                        <p class="text-[10px] font-black text-foreground-3">تخصيص للحالات الاستثنائية — رابط Canonical ثابت من النظام ولا يُعدَّل يدويًا</p>
                    </div>
                </div>
                <select x-model="sel" @change="resetDraft()" aria-label="اختر الصفحة لتحرير SEO" class="h-10 appearance-none rounded-xl border border-border bg-surface-2 px-3.5 text-xs font-black text-foreground">
                    @foreach ($pages as $k => $p)
                        <option value="{{ $k }}">{{ explode('|', $p['title'])[0] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mt-5 space-y-4">
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-black text-foreground-2">عنوان الصفحة (Title)</span>
                    <input :value="display.title" @input="draft.title = $event.target.value" @disabled(! $canEdit) class="{{ $inp }}" />
                    <p class="mt-1 text-[10px] font-bold" :class="display.title.length > 60 ? 'text-amber-500' : 'text-foreground-3'" x-text="display.title.length + ' حرف ' + (display.title.length > 60 ? '(يفضل دقة 60)' : '· جودة جيدة')"></p>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-black text-foreground-2">الوصف (Meta Description)</span>
                    <textarea rows="3" :value="display.description" @input="draft.description = $event.target.value" @disabled(! $canEdit)
                        class="w-full rounded-xl border border-border bg-surface-2 px-3.5 py-3 text-small font-bold leading-relaxed text-foreground focus:border-primary-400 focus:outline-none"></textarea>
                    <p class="mt-1 text-[10px] font-bold" :class="display.description.length > 160 ? 'text-amber-500' : 'text-foreground-3'" x-text="display.description.length + ' حرف ' + (display.description.length > 160 ? '(يفضل دقة 160)' : '· موافق لمعيار الجودة')"></p>
                </label>
                <label class="block">
                    <span class="mb-1.5 block text-[11px] font-black text-foreground-2">الكلمات المفتاحية الأساسية</span>
                    <input :value="display.primaryKeyword" @input="draft.primaryKeyword = $event.target.value" @disabled(! $canEdit) class="{{ $inp }}" />
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-black text-foreground-2">الكلمات المفتاحية الثانوية (فاصلة)</span>
                        <input :value="display.secondaryKeywords" @input="draft.secondaryKeywords = $event.target.value" @disabled(! $canEdit) class="{{ $inp }}" />
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-black text-foreground-2">Robots</span>
                        <select :value="display.robots" @change="draft.robots = $event.target.value" @disabled(! $canEdit) class="{{ $inp }} appearance-none text-xs">
                            <option value="index,follow">index, follow</option>
                            <option value="index,nofollow">index, nofollow</option>
                            <option value="noindex,nofollow">noindex, nofollow</option>
                        </select>
                    </label>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-black text-foreground-2">Canonical URL (Read-only)</span>
                        <input :value="display.canonical" disabled dir="ltr" class="num h-11 w-full rounded-xl border border-border bg-surface-3 px-3.5 text-xs font-black text-foreground-3" />
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[11px] font-black text-foreground-2">OG Image</span>
                        <input :value="display.ogImage" disabled dir="ltr" class="num h-11 w-full rounded-xl border border-border bg-surface-3 px-3.5 text-xs font-black text-foreground-3" />
                    </label>
                </div>
                @if ($canEdit)
                    <x-button full-width ::disabled="busy" @click="save()"><span x-text="saved ? 'تم الحفظ وتوثيقه ✓' : 'حفظ التعديلات وتطبيقها على الرأس'"></span></x-button>
                @endif
            </div>
        </x-card>

        <div class="space-y-5 lg:col-span-2">
            <div>
                <h4 class="mb-2 flex items-center gap-1.5 text-[11px] font-black text-foreground-3"><x-i name="chrome" class="size-3.5 text-primary" />معاينة بحث جوجل</h4>
                <div class="rounded-2xl border border-border bg-white p-4 font-sans dark:bg-slate-900" dir="rtl">
                    <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400" dir="ltr" x-text="display.canonical"></p>
                    <h4 class="mt-1 line-clamp-2 text-[15px] font-bold leading-snug text-[#1a0dab] dark:text-[#8ab4f8]" x-text="display.title"></h4>
                    <p class="mt-1 line-clamp-2 text-[12px] leading-relaxed text-slate-600 dark:text-slate-300" x-text="display.description"></p>
                    <span class="mt-2 inline-flex items-center gap-1 rounded bg-slate-100 px-1.5 py-0.5 text-[8px] font-black text-slate-500">Google Search Preview</span>
                </div>
            </div>
            <div>
                <h4 class="mb-2 flex items-center gap-1.5 text-[11px] font-black text-foreground-3"><x-i name="share-2" class="size-3.5 text-secondary" />معاينة المشاركة الاجتماعية OG</h4>
                <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-xs" dir="rtl">
                    <div class="aspect-[1.91/1] bg-surface-3"><img :src="display.ogImage" alt="" class="size-full object-cover" /></div>
                    <div class="p-3">
                        <p class="text-[9px] font-bold text-foreground-3">mathemperor.sa</p>
                        <p class="mt-0.5 truncate text-xs font-black text-foreground" x-text="display.title"></p>
                        <p class="mt-0.5 line-clamp-2 text-[10px] leading-snug text-foreground-2" x-text="display.description"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <x-card>
            <div class="flex items-center gap-2">
                <x-i name="globe-2" class="size-5 text-primary" />
                <h3 class="text-subtitle">Sitemap (<a href="/sitemap.xml" target="_blank" class="hover:underline">/sitemap.xml</a>)</h3>
                <x-badge tone="success" size="sm">{{ $seoCount }} صفحة</x-badge>
            </div>
            <pre class="num mt-3 max-h-56 overflow-auto rounded-xl border border-border bg-surface-3 p-3 font-mono text-[10px] leading-relaxed text-foreground-2" dir="ltr">{{ $sitemap }}</pre>
        </x-card>
        <x-card>
            <div class="flex items-center gap-2">
                <x-i name="file-code-2" class="size-5 text-secondary" />
                <h3 class="text-subtitle">Robots (<a href="/robots.txt" target="_blank" class="hover:underline">/robots.txt</a>)</h3>
                <x-badge tone="warning" size="sm">{{ $noindexCount }} صفحة ممنوعة</x-badge>
            </div>
            <pre class="num mt-3 max-h-56 overflow-auto rounded-xl border border-border bg-surface-3 p-3 font-mono text-[10px] leading-relaxed text-foreground-2" dir="ltr">{{ $robots }}</pre>
        </x-card>
    </div>
</div>
