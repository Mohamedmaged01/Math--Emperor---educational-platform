@props(['href' => '#top'])
<a href="{{ $href }}" {{ $attributes->class('group flex items-center gap-2.5') }} aria-label="منصة إمبراطور الرياضيات — الرئيسية">
    <span class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-primary-400 via-primary-600 to-primary-800 shadow-md shadow-primary-500/25 transition-transform duration-300 group-hover:scale-105">
        <x-i name="crown" class="size-5 text-white" stroke-width="2.4" />
    </span>
    <span class="leading-tight">
        <span class="block text-base font-black tracking-tight text-foreground">إمبراطور الرياضيات</span>
        <span class="num block text-[9px] font-bold tracking-[0.28em] text-primary">MATH EMPEROR</span>
    </span>
</a>
