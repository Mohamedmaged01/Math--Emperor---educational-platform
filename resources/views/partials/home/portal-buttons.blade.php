{{-- student gate + imperial console chip (signed-in users go straight to their portal) --}}
<div @class(['flex items-center gap-2', 'grid grid-cols-1 gap-2' => $compact])>
    <button type="button" @click="$store.portal.open('student'); open = false" aria-label="بوابة دخول الطلاب — منصة الإمبراطور"
        @class([
            'group flex items-center gap-2.5 rounded-xl border border-secondary-100 bg-secondary-soft/70 px-3.5 py-2 text-start shadow-xs transition-all duration-200 hover:border-secondary-500/50 hover:shadow-md',
            'w-full px-4 py-3' => $compact,
        ])>
        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-white/80 text-secondary shadow-xs">
            <x-i name="graduation-cap" class="size-5" />
        </span>
        <span class="leading-tight">
            <span class="block text-xs font-black text-foreground">بوابة دخول الطلاب</span>
            <span class="block text-[9px] font-bold text-foreground-3">درجاتك + تجميعاتك</span>
        </span>
    </button>

    <button type="button" @click="$store.portal.open('imperial'); open = false"
        aria-label="لوحة التحكم الإمبراطورية — الإدارة والمعلمون" title="لوحة التحكم الإمبراطورية"
        @class([
            'group grid h-11 w-11 place-items-center rounded-xl border border-primary-100 bg-primary-soft/70 shadow-xs transition-all duration-200 hover:border-primary-300 hover:shadow-md hover:-translate-y-0.5',
            'w-full' => $compact,
        ])>
        <span class="grid size-9 place-items-center rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 text-white shadow-sm transition-transform duration-300 group-hover:scale-110 group-hover:rotate-3">
            <x-i name="layout-dashboard" class="size-4.5" />
        </span>
    </button>
</div>
