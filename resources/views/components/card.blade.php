@props(['interactive' => false, 'padded' => true])
<div {{ $attributes->class([
    'rounded-2xl border border-border bg-surface shadow-sm transition-all duration-300',
    'p-5 sm:p-6' => $padded,
    'hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-lg cursor-pointer' => $interactive,
]) }}>{{ $slot }}</div>
