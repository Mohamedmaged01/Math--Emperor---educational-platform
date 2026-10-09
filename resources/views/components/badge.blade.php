@props(['tone' => 'neutral', 'size' => 'md', 'icon' => null])
@php
    $tones = [
        'neutral' => 'bg-surface-2 text-foreground-2 border-border',
        'primary' => 'bg-primary-soft text-primary border-primary-100',
        'secondary' => 'bg-secondary-soft text-secondary border-secondary-100',
        'accent' => 'bg-accent-soft text-accent border-accent-100',
        'success' => 'bg-success-soft text-success border-success-100',
        'warning' => 'bg-warning-soft text-warning border-amber-200/60',
        'danger' => 'bg-danger-soft text-danger border-red-200/60',
    ];
@endphp
<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full border font-black whitespace-nowrap',
    $tones[$tone] ?? $tones['neutral'],
    $size === 'sm' ? 'px-2.5 py-0.5 text-[11px]' : 'px-3.5 py-1.5 text-xs',
]) }}>@if ($icon)<x-i :name="$icon" class="{{ $size === 'sm' ? 'size-3' : 'size-3.5' }}" />@endif{{ $slot }}</span>
