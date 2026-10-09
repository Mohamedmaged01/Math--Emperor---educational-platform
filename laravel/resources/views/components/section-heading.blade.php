@props(['badge', 'title', 'goldTitle' => '', 'desc' => null, 'inline' => false])
<div class="mx-auto max-w-3xl text-center">
    <div data-reveal style="--ry:16px;--rd:.6s">
        <x-badge tone="primary" icon="sparkle">{{ $badge }}</x-badge>
    </div>
    <h2 data-reveal style="--ry:26px;--rd:.8s;--rdelay:.1s" class="mt-5 text-headline text-foreground">
        @if ($inline)
            {{ $title }} <span class="gradient-text py-1">{{ $goldTitle }}</span>
        @else
            {{ $title }}<span class="gradient-text block py-1">{{ $goldTitle }}</span>
        @endif
    </h2>
    @if ($desc)
        <p data-reveal style="--ry:20px;--rd:.8s;--rdelay:.2s" class="mt-4 text-body-lg text-foreground-2">{{ $desc }}</p>
    @endif
    <div data-reveal="line" style="--rd:1s;--rdelay:.3s" class="section-line mx-auto mt-8 w-40"></div>
</div>
