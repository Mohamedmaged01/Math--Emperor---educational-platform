@php
    $reviewing = count(array_filter($ai, fn ($a) => str_contains((string) ($a['status'] ?? ''), 'مراجعة')));
    $statusTone = fn ($s) => str_contains($s, 'مراجعة') ? 'warning' : (str_contains($s, 'مرفوض') ? 'danger' : 'success');
@endphp
<div class="space-y-4">
    <div class="grid gap-3 sm:grid-cols-4">
        @foreach ([['طلبات AI اليوم', 128], ['توكن مستهلك', '486K'], ['بانتظار مراجعة بشرية', $reviewing], ['رضا المستخدمين', '94%']] as [$l, $v])
            <x-card class="p-4! text-center"><p class="num text-2xl font-black text-foreground">{{ $v }}</p><p class="text-caption text-foreground-3">{{ $l }}</p></x-card>
        @endforeach
    </div>
    <x-card :padded="false">
        <div class="border-b border-border px-5 py-4"><h3 class="text-subtitle">أحدث طلبات المحرك — التكوين البشري إلزامي للنشر</h3></div>
        <ul class="divide-y divide-border">
            @foreach ($ai as $a)
                <li class="flex flex-wrap items-center gap-3 px-5 py-3.5">
                    <span class="grid size-10 place-items-center rounded-xl bg-accent-soft text-accent"><x-i name="sparkles" class="size-4.5" /></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-small font-black text-foreground">{{ $a['user'] ?? '' }} — وضع «{{ $a['mode'] ?? '' }}»</p>
                        <p class="num text-[10px] font-bold text-foreground-3" dir="ltr">{{ $a['tokens'] ?? '' }} tokens · {{ $a['at'] ?? '' }}</p>
                    </div>
                    <x-badge :tone="$statusTone((string) ($a['status'] ?? ''))" size="sm">{{ $a['status'] ?? '' }}</x-badge>
                </li>
            @endforeach
        </ul>
    </x-card>
    <x-card class="border-accent-100 bg-accent-soft/60">
        <p class="text-small font-bold leading-relaxed text-foreground-2">
            <x-i name="lock-keyhole" class="inline size-4 text-accent" /> لا تُعرض مفاتيح أو أسرار مزوّدين في هذه المساحة أبدًا، والنظام يمنع وصول أي سياق AI إلى بيانات مستخدم آخر. كل توليد أكاديمي يبقى في «بانتظار مراجعة بشرية» حتى اعتماده.
        </p>
    </x-card>
</div>
