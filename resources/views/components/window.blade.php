@props(['show', 'close', 'label', 'heading', 'sub', 'subTone' => 'text-primary', 'closeLabel'])
{{-- full-screen "independent page" window: sticky chrome bar, ESC to close, scroll-locked body --}}
<template x-teleport="body">
    <div x-show="{{ $show }}" x-cloak role="dialog" aria-modal="true" aria-label="{{ $label }}"
        x-effect="$store.lock.set('window-{{ md5($label) }}', !!({{ $show }}))"
        @keydown.escape.window="if ({{ $show }}) { {{ $close }} }"
        x-transition:enter="transition duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[70] overflow-y-auto bg-background">
        <header x-show="{{ $show }}"
            x-transition:enter="transition duration-[450ms] ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="-translate-y-16" x-transition:enter-end="translate-y-0"
            class="sticky top-0 z-10 border-b border-border bg-surface/85 backdrop-blur-xl">
            <div class="mx-auto flex h-16 max-w-6xl items-center gap-3 px-4 sm:px-6">
                <button type="button" @click="{{ $close }}" aria-label="العودة إلى الصفحة الرئيسية"
                    class="group flex items-center gap-2 rounded-xl px-2 py-2 text-sm font-black text-foreground-2 transition-colors hover:text-primary">
                    <span class="grid size-9 place-items-center rounded-xl border border-border bg-surface shadow-xs transition-colors group-hover:border-primary-300 group-hover:text-primary">
                        <x-i name="arrow-right" class="size-4.5" />
                    </span>
                    <span class="hidden sm:inline">الرئيسية</span>
                </button>
                <div class="mx-auto text-center" aria-hidden="true">
                    <p class="text-sm font-black text-foreground">{{ $heading }}</p>
                    <p class="num text-[9px] font-bold tracking-[0.28em] {{ $subTone }}">{{ $sub }}</p>
                </div>
                <button type="button" @click="{{ $close }}" aria-label="{{ $closeLabel }}"
                    x-init="$watch(() => !!({{ $show }}), v => v && setTimeout(() => $el.focus(), 60))"
                    class="grid size-10 place-items-center rounded-xl border border-border bg-surface text-foreground-3 shadow-xs transition-colors hover:border-danger/40 hover:text-danger">
                    <x-i name="x" class="size-5" />
                </button>
            </div>
        </header>
        {{ $slot }}
    </div>
</template>
