<button type="button" x-data @click="$store.theme.cycle()"
    :aria-label="'تبديل المظهر — الحالي: ' + $store.theme.label" :title="$store.theme.label"
    {{ $attributes->class('grid size-10 place-items-center rounded-xl border border-border bg-surface text-foreground-2 shadow-xs transition-all duration-200 hover:border-primary-300 hover:text-primary focus-visible:outline-2 focus-visible:outline-primary-500') }}>
    <span x-show="$store.theme.theme === 'light'"><x-i name="sun" class="size-4.5" /></span>
    <span x-show="$store.theme.theme === 'dark'" x-cloak><x-i name="moon" class="size-4.5" /></span>
    <span x-show="$store.theme.theme === 'system'" x-cloak><x-i name="monitor-smartphone" class="size-4.5" /></span>
</button>
