@php
    $statIcon = fn ($k) => match ((string) $k) {
        'crown' => 'crown', 'trophy' => 'trophy', 'book' => 'book-open-check',
        'target' => 'target', 'users' => 'users', default => 'trending-up',
    };
@endphp
{{-- hidden entirely when every stat is switched off from the imperial console --}}
@if (count($stats))
<section class="relative py-14 sm:py-16" aria-label="إحصاءات المنصة">
    <div class="mx-auto max-w-7xl px-4 sm:px-6">
        <div class="grid grid-cols-2 gap-3.5 lg:grid-cols-4 lg:gap-5">
            @foreach ($stats as $i => $s)
                <div data-reveal style="--ry:30px;--rd:.7s;--rdelay:{{ $i * 0.1 }}s">
                    <x-card class="text-center">
                        <span class="mx-auto mb-3 grid size-12 place-items-center rounded-2xl bg-primary-soft text-primary">
                            <x-i :name="$statIcon($s['iconKey'] ?? '')" class="size-6" />
                        </span>
                        <div class="text-2xl font-black text-foreground tabular-nums sm:text-3xl">
                            <span class="num" x-data="counter({{ (float) ($s['value'] ?? 0) }}, {{ (int) ($s['decimals'] ?? 0) }})" x-intersect.once.margin.-80px="run()"><span x-text="display">0</span><span class="text-primary">{{ $s['suffix'] ?? '' }}</span></span>
                        </div>
                        <p class="mt-1 text-caption text-foreground-3">{{ $s['label'] ?? '' }}</p>
                    </x-card>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
