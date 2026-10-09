@props([
    'show',              // Alpine expression → open state
    'close',             // Alpine statement → closes the modal
    'size' => 'md',
    'title' => null,     // static title
    'titleExpr' => null, // reactive title (Alpine expression)
    'description' => null,
    'z' => 'z-[80]',
    'plain' => false,    // no default padding/close button (custom chrome)
])
@php
    $sizes = ['sm' => 'max-w-md', 'md' => 'max-w-xl', 'lg' => 'max-w-3xl'];
    $lock = 'modal-'.\Illuminate\Support\Str::random(6);
@endphp
<template x-teleport="body">
    <div x-show="{{ $show }}" x-cloak
        x-transition:leave="duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-100"
        class="fixed inset-0 {{ $z }} grid place-items-center overflow-y-auto p-4"
        x-effect="$store.lock.set('{{ $lock }}', !!({{ $show }}))"
        @keydown.escape.window="if ({{ $show }}) { {{ $close }} }">
        <div x-show="{{ $show }}" x-transition.opacity.duration.180ms @click="{{ $close }}"
            class="fixed inset-0 bg-foreground/40 backdrop-blur-sm" aria-hidden="true"></div>
        <div x-show="{{ $show }}"
            x-transition:enter="transition duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]"
            x-transition:enter-start="opacity-0 translate-y-6 scale-[0.97]"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition duration-200 ease-in"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-[0.98]"
            x-init="$watch(() => !!({{ $show }}), v => v && setTimeout(() => $el.focus(), 40))"
            role="dialog" aria-modal="true" tabindex="-1"
            @if ($title) aria-label="{{ $title }}" @endif
            {{ $attributes->class(['relative w-full rounded-2xl border border-border bg-surface shadow-2xl focus:outline-none', $sizes[$size] ?? $sizes['md'], 'p-6 sm:p-7' => ! $plain]) }}>
            @unless ($plain)
                <button type="button" @click="{{ $close }}" aria-label="إغلاق"
                    class="absolute top-4 left-4 z-10 grid size-9 place-items-center rounded-xl text-foreground-3 transition-colors hover:bg-surface-2 hover:text-foreground">
                    <x-i name="x" class="size-4.5" />
                </button>
            @endunless
            @if ($title)
                <h2 class="text-title text-foreground">{{ $title }}</h2>
            @elseif ($titleExpr)
                <h2 class="text-title text-foreground" x-text="{{ $titleExpr }}"></h2>
            @endif
            @if ($description)
                <p class="mt-1 text-small text-foreground-2">{{ $description }}</p>
            @endif
            <div @class(['mt-5' => $title || $titleExpr || $description])>{{ $slot }}</div>
        </div>
    </div>
</template>
