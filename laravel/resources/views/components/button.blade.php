@props(['variant' => 'primary', 'size' => 'md', 'icon' => null, 'fullWidth' => false, 'type' => 'button', 'href' => null])
@php
    $variants = [
        'primary' => 'bg-gradient-to-l from-primary-500 to-primary-700 text-white shadow-md shadow-primary-500/25 hover:shadow-lg hover:shadow-primary-500/30 hover:brightness-110',
        'secondary' => 'bg-secondary-soft text-secondary border border-secondary-100 hover:bg-secondary-100',
        'outline' => 'border border-border-strong bg-surface text-foreground shadow-xs hover:border-primary-300 hover:text-primary',
        'ghost' => 'text-foreground-2 hover:bg-surface-2 hover:text-foreground',
        'danger' => 'bg-danger-soft text-danger border border-danger/20 hover:brightness-95',
        'success' => 'bg-success-soft text-success border border-success/20 hover:brightness-95',
        'link' => 'text-primary underline-offset-4 hover:underline rounded-md px-1',
    ];
    $sizes = ['sm' => 'h-9 px-4 text-sm', 'md' => 'h-11 px-6 text-sm', 'lg' => 'h-13 px-8 text-base'];
    $classes = [
        'relative inline-flex items-center justify-center gap-2 rounded-xl font-black transition-all duration-200 select-none disabled:pointer-events-none disabled:opacity-55 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500 active:scale-[0.985]',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        'w-full' => $fullWidth,
    ];
@endphp
@if ($href)
<a href="{{ $href }}" {{ $attributes->class($classes) }}>@if ($icon)<x-i :name="$icon" class="size-4" />@endif{{ $slot }}</a>
@else
<button type="{{ $type }}" {{ $attributes->class($classes) }}>@if ($icon)<x-i :name="$icon" class="size-4" />@endif{{ $slot }}</button>
@endif
