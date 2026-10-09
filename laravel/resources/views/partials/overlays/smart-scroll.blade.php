<button type="button" x-data="smartScroll" @click="go()"
    :aria-label="down ? 'النزول إلى القسم التالي' : 'العودة إلى أعلى الصفحة'" :title="down ? 'النزول إلى القسم التالي' : 'العودة إلى أعلى الصفحة'"
    style="bottom: max(1.25rem, calc(env(safe-area-inset-bottom) + 0.75rem))"
    class="anim-pop fixed right-4 z-[65] grid size-12 place-items-center rounded-2xl border border-primary-200 bg-gradient-to-b from-primary-500 to-primary-700 text-white shadow-lg shadow-primary-500/30 ring-1 ring-white/30 ring-inset transition-all duration-200 hover:scale-[1.06] hover:shadow-xl hover:shadow-primary-500/40 hover:brightness-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500 active:scale-[0.96] sm:right-6">
    <span class="animate-pulse-ring absolute inset-0 rounded-2xl ring-1 ring-primary-300/50" aria-hidden="true"></span>
    <span x-show="down" class="anim-nudge-down relative block"><x-i name="chevron-down" class="size-6" stroke-width="2.6" /></span>
    <span x-show="!down" x-cloak class="anim-nudge-up relative block"><x-i name="chevron-up" class="size-6" stroke-width="2.6" /></span>
</button>
