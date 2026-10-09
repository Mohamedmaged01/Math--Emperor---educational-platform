@php $faqs = \App\Support\Content::faqs(); @endphp
@push('head')
    <script type="application/ld+json">{!! json_encode(\App\Support\Seo::faqSchema(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush
<section id="faq" x-data="{ open: 0 }" class="relative py-20 sm:py-28">
    <div class="absolute bottom-0 -right-40 h-[360px] w-[360px] rounded-full bg-secondary-100/50 blur-[130px] dark:bg-secondary-500/6" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-4xl px-4 sm:px-6">
        <x-section-heading badge="الأسئلة الشائعة" title="كل ما يدور في ذهنك —" :inline="true" gold-title="نجيب عنه بوضوح" />

        <div class="mt-12 space-y-3">
            @foreach ($faqs as $i => $f)
                <div data-reveal style="--ry:24px;--rd:.6s;--rdelay:{{ $i * 0.06 }}s"
                    :class="open === {{ $i }} ? 'border-primary-200 shadow-md' : 'border-border shadow-sm'"
                    class="overflow-hidden rounded-2xl border bg-surface transition-all duration-300">
                    <button type="button" @click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="open === {{ $i }}"
                        class="flex w-full items-center gap-3.5 px-5 py-4.5 text-right sm:px-6 sm:py-5">
                        <span :class="open === {{ $i }} ? 'border-primary-200 bg-primary-soft text-primary' : 'border-border bg-surface-2 text-foreground-3'"
                            class="grid size-9 shrink-0 place-items-center rounded-xl border transition-colors duration-300" aria-hidden="true">
                            <x-i name="message-circle-question" class="size-4.5" />
                        </span>
                        <span :class="open === {{ $i }} ? 'text-primary' : 'text-foreground'" class="flex-1 text-small font-black transition-colors duration-300 sm:text-base">{{ $f['q'] }}</span>
                        <span :class="open === {{ $i }} ? 'rotate-45 border-primary-500 bg-primary-500 text-white' : 'border-border text-foreground-3'"
                            class="grid size-8 shrink-0 place-items-center rounded-full border transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" aria-hidden="true">
                            <x-i name="plus" class="size-4" stroke-width="3" />
                        </span>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse.duration.400ms @if ($i !== 0) x-cloak @endif>
                        <p class="px-5 pb-5 pr-18 text-small leading-loose text-foreground-2 sm:px-6 sm:pr-19">{{ $f['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
