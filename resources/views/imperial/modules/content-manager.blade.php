@php
    $groupsCm = [
        ['id' => 'about', 'label' => 'من نحن', 'icon' => 'graduation-cap', 'tone' => 'primary'],
        ['id' => 'courses', 'label' => 'الدورات وشروحات', 'icon' => 'file-text', 'tone' => 'accent'],
        ['id' => 'simulator', 'label' => 'المحاكي', 'icon' => 'radio', 'tone' => 'secondary'],
        ['id' => 'marquee', 'label' => 'شريط المتحرك', 'icon' => 'radio', 'tone' => 'warning'],
        ['id' => 'plans', 'label' => 'الخطط الذكية', 'icon' => 'calendar-range', 'tone' => 'primary'],
        ['id' => 'footer', 'label' => 'جهات الاتصال والنشرة', 'icon' => 'message-square-text', 'tone' => 'secondary'],
    ];
    $inp = 'h-11 w-full rounded-xl border border-border bg-surface-2 px-3.5 text-small font-black text-foreground focus:border-primary-400 focus:outline-none';
@endphp
<div x-data="contentManager(@js($contentRows), @js($groupsCm))" class="space-y-6">
    <div class="flex flex-wrap items-center gap-4">
        <div class="min-w-0 flex-1">
            <h2 class="text-title text-foreground">إدارة المحتوى — تعديل أقسام صفحات المنصة مركزيًا</h2>
            <p class="mt-1 text-small font-bold leading-relaxed text-foreground-3">تحكم في محتوى كل قسم: الهيكل، العنوان، النص، وسقف الظهور — بدون لمس الكود والتدقيق موثّق في كل خطوة.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-primary-100 bg-primary-soft px-3.5 py-1.5 text-xs font-black text-primary"><span class="num" x-text="visible.length"></span> قسم قابل للبناء</span>
            @if ($canManage)
                <x-button size="sm" icon="pencil" @click="beginEdit(null)">إنشاء قسم جديد</x-button>
            @endif
        </div>
    </div>

    <div class="flex flex-wrap gap-2" role="tablist" aria-label="مجموعات الإدارة">
        @foreach ($groupsCm as $g)
            <button type="button" role="tab" :aria-selected="sel === @js($g['id'])" @click="sel = @js($g['id'])"
                :class="sel === @js($g['id']) ? 'border-primary-300 bg-primary-500 text-white shadow-md shadow-primary-500/25' : 'border-border bg-surface text-foreground-2 hover:border-primary-300 hover:text-primary'"
                class="group flex items-center gap-2 rounded-full border px-4 py-2 text-xs font-black transition-all">
                <span :class="sel === @js($g['id']) ? 'text-white' : 'text-primary'"><x-i :name="$g['icon']" class="size-4" /></span>
                {{ $g['label'] }}
            </button>
        @endforeach
    </div>

    <div class="relative flex-1">
        <x-i name="search" class="pointer-events-none absolute inset-y-0 start-3.5 my-auto size-4.5 text-foreground-3" />
        <input x-model="q" placeholder="ابحث داخل أقسام الصفحة المختارة: العنوان، الملاحظات، الفئة، التصنيف..." aria-label="بحث في محتوى القسم المختار"
            class="h-11 w-full rounded-xl border border-border bg-surface ps-10.5 pe-4 text-small font-bold text-foreground placeholder:text-foreground-3 focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
    </div>

    <div class="grid gap-4" role="list">
        <div x-show="visible.length === 0" x-cloak>
            <x-card class="py-14 text-center">
                <x-i name="file-text" class="mx-auto size-10 text-foreground-3" />
                <h3 class="mt-4 text-title text-foreground">لا أقسام قابلة للعرض تحت هذا الفلتر</h3>
                <p class="mt-2 text-small font-black text-foreground-3">صمّم قسمك الأول بزر «إنشاء قسم جديد»</p>
            </x-card>
        </div>
        <template x-for="(row, i) in visible" :key="row._dataset + row.id">
            <article role="listitem" class="anim-slide-x group relative" :style="'animation-delay:' + Math.min(i, 12) * 60 + 'ms'">
                <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm">
                    <div class="flex flex-wrap items-center gap-3.5 border-b border-border bg-surface-2/60 px-5 py-4">
                        <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-primary-400 to-primary-700 text-lg font-black text-white shadow-md" x-text="row._title.charAt(0)"></span>
                        <div class="min-w-0 flex-1">
                            <h3 class="truncate text-subtitle text-foreground" x-text="row._title"></h3>
                            <p class="truncate text-[10px] font-bold text-foreground-3" x-text="row._sub"></p>
                        </div>
                        <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black" :class="groupTone()" x-text="groupLabel()"></span>
                    </div>
                    <div class="grid gap-3 p-5 text-xs font-bold text-foreground-2 sm:grid-cols-2">
                        <p class="flex items-center gap-2"><span class="font-black text-foreground-3">الوحدة:</span> <span x-text="sel"></span></p>
                        <p class="flex items-center gap-2"><span class="font-black text-foreground-3">النوع:</span> <span x-text="String(row.type ?? row.kind ?? 'محتوى')"></span></p>
                        <p x-show="String(row.title ?? '').length > 0" class="flex items-center gap-2 sm:col-span-2"><span class="font-black text-foreground-3">العنوان الحالي:</span> <span class="truncate" x-text="row.title"></span></p>
                        <p x-show="String(row.description ?? '').length > 0" class="flex items-center gap-2 sm:col-span-2"><span class="font-black text-foreground-3">الوصف:</span> <span class="truncate" x-text="row.description"></span></p>
                    </div>
                    @if ($canManage)
                        <div class="flex flex-wrap items-center gap-2 border-t border-border px-5 py-3.5">
                            <x-button size="sm" variant="outline" icon="pencil" @click="beginEdit(row)">تعديل المحتوى</x-button>
                            <button type="button" @click="toggleVisible(row)"
                                :class="row.enabled !== false ? 'border-warning/40 bg-warning-soft text-warning' : 'border-success/40 bg-success-soft text-success'"
                                class="relative inline-flex h-9 items-center justify-center gap-1.5 rounded-xl border px-4 text-sm font-black transition-all duration-200 hover:brightness-105">
                                <span x-show="row.enabled !== false" class="inline-flex items-center gap-1.5"><x-i name="eye-off" class="size-4" /> إخفاء من المنصة</span>
                                <span x-show="row.enabled === false" class="inline-flex items-center gap-1.5"><x-i name="eye" class="size-4" /> إظهار في المنصة</span>
                            </button>
                            <x-button size="sm" variant="danger" icon="trash-2" @click="deleting = row; reason = ''">حذف بالسبب</x-button>
                        </div>
                    @endif
                </div>
            </article>
        </template>
    </div>

    <x-modal show="adding" close="adding = false" title-expr="edited === null ? 'إنشاء قسم جديد' : 'تعديل: ' + (edited?.title ?? edited?._title ?? '')" size="lg">
        <div class="space-y-4">
            <div x-show="edited === null" class="rounded-2xl border border-primary-100 bg-primary-soft/60 p-4 text-xs font-bold text-foreground-2" x-text="'أنت الآن في وضع إنشاء قسم جديد في مجموعة «' + groupLabel() + '» — سيُنشر فورًا ويُوثّق في سجل التدقيق.'"></div>
            <div x-show="edited !== null" class="rounded-2xl border border-secondary-100 bg-secondary-soft/50 p-4 text-xs font-bold leading-relaxed text-foreground-2" x-text="'تعديل القسم «' + (edited?._title ?? '') + '» — يُحفظ التعديل مباشرة في مصدر بياناته ويُوثّق في سجل التدقيق.'"></div>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block sm:col-span-2"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">العنوان</span><input x-model="form.title" class="{{ $inp }}" /></label>
                <label class="block sm:col-span-2"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">وصف / محتوى</span>
                    <textarea rows="4" x-model="form.description" class="w-full rounded-xl border border-border bg-surface-2 px-3.5 py-3 text-small font-bold leading-relaxed text-foreground focus:border-primary-400 focus:outline-none"></textarea>
                </label>
                <label class="block"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">الكلمات المفتاحية (فاصلة)</span><input x-model="form.keywords" class="{{ $inp }}" /></label>
                <label class="block"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">robots</span>
                    <select x-model="form.robots" class="{{ $inp }} appearance-none text-xs">
                        <option value="index,follow">index, follow</option><option value="index,nofollow">index, nofollow</option><option value="noindex,nofollow">noindex, nofollow</option>
                    </select>
                </label>
                <label class="block sm:col-span-2"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">OG Image URL (اختياري)</span><input x-model="form.ogImage" dir="ltr" placeholder="/images/course-section-banner.jpg" class="num {{ $inp }}" /></label>
            </div>
            <div class="flex items-center gap-2.5">
                <x-button full-width ::disabled="!form.title.trim() || busy" @click="save()"><span x-text="saved ? 'تم الحفظ ✓' : (edited === null ? 'إنشاء وتوثيق' : 'حفظ وتوثيق التعديل')"></span></x-button>
                <x-button variant="ghost" @click="adding = false">إلغاء</x-button>
            </div>
        </div>
    </x-modal>

    <x-modal show="deleting" close="deleting = null" title="تأكيد حذف القسم" size="sm">
        <div class="space-y-4">
            <p class="text-small font-bold leading-relaxed text-foreground-2">سيُحذف القسم <span class="num font-black text-danger" x-text="deleting?.title || deleting?._title"></span> بعد تقديم سبب وجيه — وسيُوثّق في سجل التدقيق.</p>
            <input x-model="reason" placeholder="سبب الحذف (إلزامي)…" class="h-11 w-full rounded-xl border border-border bg-surface-2 px-4 text-small font-bold focus:border-danger/50 focus:outline-none" />
            <div class="flex gap-2">
                <x-button variant="danger" full-width ::disabled="!reason.trim() || busy" @click="remove()">حذف نهائي وموثق</x-button>
                <x-button variant="ghost" @click="deleting = null">إلغاء</x-button>
            </div>
        </div>
    </x-modal>
</div>
