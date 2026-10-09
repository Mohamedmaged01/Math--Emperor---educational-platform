<div x-data="cmsPage(@js(['heroLine1' => $cms['heroLine1'] ?? '', 'heroLine2Gold' => $cms['heroLine2Gold'] ?? '', 'heroLine3' => $cms['heroLine3'] ?? '']))" class="mx-auto max-w-2xl space-y-5">
    <x-card>
        <h3 class="text-subtitle">محرر قسم البطل — Hero</h3>
        <p class="mt-1 text-small text-foreground-3">
            أي تعديل يُحفظ ويُنشر ينعكس <span class="font-black text-primary">فورًا على الصفحة الرئيسية</span> عند إعادة تحميلها — بلا لمس الكود. سيُوثق النشر في سجل التدقيق.
        </p>
        <div class="mt-5 space-y-4">
            @foreach (['heroLine1' => 'سطر العنوان الأول', 'heroLine2Gold' => 'سطر الذهب (المتوهج)', 'heroLine3' => 'سطر العنوان الأخير'] as $k => $label)
                <label class="block">
                    <span class="mb-1.5 block text-xs font-black text-foreground-2">{{ $label }}</span>
                    <input x-model="vals.{{ $k }}" @disabled(! $canEdit)
                        class="h-11 w-full rounded-xl border border-border bg-surface-2 px-4 text-small font-black text-foreground focus:border-primary-400 focus:outline-none" />
                </label>
            @endforeach
        </div>
        <div class="mt-6 flex gap-2">
            <x-button full-width ::disabled="!dirty || busy" @click="publish()">نشر على الواجهة</x-button>
            <x-button variant="ghost" ::disabled="!dirty" @click="vals = { ...saved }">تراجع</x-button>
        </div>
        <p x-show="dirty" x-cloak class="mt-3 text-center text-[11px] font-black text-warning">تغييرات غير منشورة — ستبقى محفوظة كمسودة حتى النشر</p>
        <p x-show="published" x-cloak class="mt-3 text-center text-[11px] font-black text-success">تم النشر ✓ — أعد تحميل الصفحة الرئيسية لرؤية العنوان الجديد</p>
        @unless ($canEdit)
            <p class="mt-3 text-center text-[11px] font-black text-danger">صلاحيتك قراءة فقط لوحدة CMS</p>
        @endunless
    </x-card>
</div>
