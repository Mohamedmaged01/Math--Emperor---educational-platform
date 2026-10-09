@php $navLinks = \App\Support\Content::navLinks(); @endphp
<header x-data="{ scrolled: false, open: false }"
    x-init="const f = () => scrolled = window.scrollY > 24; f(); window.addEventListener('scroll', f, { passive: true })"
    :class="scrolled ? 'py-2.5' : 'py-4 sm:py-5'"
    class="anim-header fixed inset-x-0 top-0 z-50 py-4 transition-all duration-300 sm:py-5">
    <div class="mx-auto max-w-[90rem] px-3 sm:px-5">
        <nav aria-label="القائمة الرئيسية"
            :class="scrolled ? 'glass shadow-md' : 'border border-transparent bg-transparent'"
            class="flex items-center justify-between gap-3 rounded-2xl border border-transparent px-3.5 py-2.5 transition-all duration-300 sm:px-4">
            <x-logo />

            {{-- main menu --}}
            <ul class="hidden items-center gap-0.5 lg:flex">
                @foreach ($navLinks as $link)
                    @php $navCls = 'relative rounded-lg px-3.5 py-2 text-small font-black text-foreground-2 transition-colors duration-200 after:absolute after:inset-x-3.5 after:bottom-1 after:h-0.5 after:origin-center after:scale-x-0 after:rounded-full after:bg-primary-500 after:transition-transform after:duration-300 hover:text-primary hover:after:scale-x-100'; @endphp
                    <li>
                        @if ($link['event'])
                            <button type="button" @click="$dispatch('{{ $link['event'] }}')" class="{{ $navCls }}">{{ $link['label'] }}</button>
                        @else
                            <a href="{{ $link['href'] }}" class="{{ $navCls }}">{{ $link['label'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>

            {{-- actions --}}
            <div class="flex items-center gap-2">
                <button type="button" @click="$dispatch('emperor:open-search')" aria-label="البحث في المنصة" title="البحث في المنصة"
                    class="grid size-10 place-items-center rounded-xl border border-border bg-surface text-foreground-2 shadow-xs transition-colors hover:border-primary-300 hover:text-primary">
                    <x-i name="search" class="size-4.5" />
                </button>
                <x-theme-toggle class="hidden md:grid" />
                <div class="hidden xl:block">
                    @include('partials.home.portal-buttons', ['compact' => false])
                </div>
                <div class="hidden 2xl:ml-1 2xl:block">
                    <a href="#pricing"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-5 py-2.5 text-small font-black text-white shadow-md shadow-primary-500/25 transition-all duration-200 hover:shadow-lg hover:brightness-110 active:scale-[0.98]">
                        <x-i name="sparkles" class="size-4" />
                        ابدأ مجانًا
                    </a>
                </div>
                <button type="button" @click="open = !open" :aria-expanded="open" :aria-label="open ? 'إغلاق القائمة' : 'فتح القائمة'"
                    class="grid size-10 place-items-center rounded-xl border border-border bg-surface text-foreground-2 shadow-xs xl:hidden">
                    <span x-show="!open"><x-i name="menu" class="size-5" /></span>
                    <span x-show="open" x-cloak><x-i name="x" class="size-5" /></span>
                </button>
            </div>
        </nav>

        {{-- mobile / tablet panel --}}
        <div x-show="open" x-cloak
            x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0 -translate-y-3 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 -translate-y-3 scale-[0.98]"
            class="glass mt-2 rounded-2xl p-3 shadow-lg xl:hidden">
            <button type="button" @click="open = false; $dispatch('emperor:open-search')"
                class="mb-2 flex w-full items-center gap-2.5 rounded-xl border border-primary-200 bg-primary-soft/60 px-4 py-3 text-small font-black text-primary transition-colors hover:bg-primary-100">
                <x-i name="search" class="size-4.5" />
                البحث في المنصة…
            </button>
            <ul class="grid gap-0.5 sm:grid-cols-2">
                @foreach ($navLinks as $i => $link)
                    <li class="anim-slide-x" style="animation-delay: {{ $i * 40 }}ms">
                        @if ($link['event'])
                            <button type="button" @click="$dispatch('{{ $link['event'] }}'); open = false"
                                class="w-full rounded-xl px-4 py-3 text-start text-small font-black text-foreground transition-colors hover:bg-surface-2 hover:text-primary">{{ $link['label'] }}</button>
                        @else
                            <a href="{{ $link['href'] }}" @click="open = false"
                                class="block rounded-xl px-4 py-3 text-small font-black text-foreground transition-colors hover:bg-surface-2 hover:text-primary">{{ $link['label'] }}</a>
                        @endif
                    </li>
                @endforeach
            </ul>
            <div class="mt-2 border-t border-border pt-3">
                @include('partials.home.portal-buttons', ['compact' => true])
            </div>
            <div class="mt-2 flex items-center gap-2">
                <a href="#pricing" @click="open = false"
                    class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-5 py-3 text-small font-black text-white shadow-md">
                    <x-i name="sparkles" class="size-4" />
                    ابدأ مجانًا
                </a>
                <x-theme-toggle class="md:hidden" />
            </div>
        </div>
    </div>
</header>
