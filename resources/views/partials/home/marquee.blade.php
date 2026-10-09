@php $topics = \App\Support\Content::topics(); @endphp
<div class="relative overflow-hidden border-y border-border bg-surface py-4" aria-label="موضوعات المنصة">
    <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-24 bg-gradient-to-l from-(--surface) to-transparent"></div>
    <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-24 bg-gradient-to-r from-(--surface) to-transparent"></div>
    <div class="marquee-track" dir="ltr">
        @foreach (array_merge($topics, $topics) as $t)
            <div class="mx-2 flex shrink-0 items-center gap-2.5 rounded-full border border-border bg-surface-2 px-4 py-2" dir="rtl">
                <x-i :name="$t['icon']" class="size-4 text-primary" />
                <span class="whitespace-nowrap text-small font-black text-foreground-2">{{ $t['label'] }}</span>
            </div>
        @endforeach
    </div>
</div>
