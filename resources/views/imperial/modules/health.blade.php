@php
    $meta = [
        'HEALTHY' => ['success', 'سليم'], 'WARNING' => ['warning', 'تحذير'], 'DEGRADED' => ['warning', 'متدهور'],
        'DOWN' => ['danger', 'متوقف'], 'UNKNOWN' => ['neutral', 'غير معروف'],
    ];
@endphp
<div class="space-y-4" x-data="{ t: new Date() }" x-init="setInterval(() => t = new Date(), 2000)">
    <x-card class="border-secondary-100 bg-secondary-soft/60">
        <p class="text-small font-bold leading-relaxed text-foreground-2">
            <x-i name="shield-check" class="inline size-4 text-secondary" /> هذا المرصد يعرض فقط ما هو <span class="font-black">حقيقي فعلًا</span>. الخدمات غير المُهيأة تُصنّف UNKNOWN — لا تُختلق حالات خضراء. آخر نبض تحقق:
            <span class="num" dir="ltr" x-text="' ' + t.toLocaleTimeString('en-GB')"></span>
        </p>
    </x-card>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($services as $i => $s)
            <div class="anim-rise" style="animation-delay: {{ $i * 60 }}ms">
                <x-card>
                    <div class="flex items-center justify-between">
                        <h4 class="text-small font-black text-foreground">{{ $s['name'] }}</h4>
                        <x-badge :tone="$meta[$s['status']][0]" size="sm">{{ $meta[$s['status']][1] }}</x-badge>
                    </div>
                    <div class="mt-3 flex items-end justify-between">
                        <p class="num text-xl font-black text-foreground">{{ $s['ms'] === null ? '—' : $s['ms'].'ms' }}</p>
                        <p class="max-w-40 text-[10px] font-bold leading-relaxed text-foreground-3">{{ $s['note'] }}</p>
                    </div>
                </x-card>
            </div>
        @endforeach
    </div>
</div>
