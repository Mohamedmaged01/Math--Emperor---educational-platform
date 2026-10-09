@props(['display' => false])
@if ($display)
<div {{ $attributes->class('my-3 flex w-full justify-center rounded-xl border border-primary-100 bg-primary-soft/60 px-4 py-3') }}>
    <span class="equation text-lg text-primary-900 dark:text-primary-100">{{ $slot }}</span>
</div>
@else
<span {{ $attributes->class('equation rounded-md bg-surface-2 px-2 py-0.5 text-[0.95em] text-foreground') }}>{{ $slot }}</span>
@endif
