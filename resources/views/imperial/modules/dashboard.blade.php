@php $riskTone = fn ($r) => $r === 'عالٍ' ? 'danger' : ($r === 'متوسط' ? 'warning' : 'neutral'); @endphp
<div class="space-y-6">
    <div class="anim-rise relative overflow-hidden rounded-3xl bg-gradient-to-bl from-primary-800 via-primary-700 to-[#14224e] p-6 text-white sm:p-8">
        <div class="absolute inset-0 opacity-15" style="background-image: radial-gradient(circle at 85% 15%, rgba(255,255,255,0.4) 0, transparent 40%)" aria-hidden="true"></div>
        <div class="relative flex flex-wrap items-center gap-4">
            <span class="grid size-14 place-items-center rounded-2xl bg-white/12 ring-1 ring-white/25 backdrop-blur"><x-i name="crown" class="size-7 text-amber-300" /></span>
            <div class="flex-1">
                <h2 class="text-title text-white">مرحبًا بك، {{ $user->name }}</h2>
                <p class="mt-0.5 text-xs font-bold text-primary-100/90">الإدارة العليا — نظام الإدارة والتحكم المركزي · جلستك موثقة في سجل التدقيق</p>
            </div>
            <span class="equation num rounded-md bg-white/10 px-2 py-0.5 text-[0.95em] text-white">Σ platform ops = LIVE</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3.5 lg:grid-cols-4">
        @foreach ($kpis as $i => $k)
            <button type="button" @click="go(@js($k['d']), {{ $canView[$k['d']] ? 'true' : 'false' }})" style="animation-delay: {{ 80 + $i * 70 }}ms"
                class="anim-rise rounded-2xl border border-border bg-surface p-5 text-start shadow-sm transition-all hover:-translate-y-0.5 hover:border-primary-200 hover:shadow-md">
                <span class="grid size-10 place-items-center rounded-xl bg-primary-soft text-primary"><x-i :name="$k['icon']" class="size-5" /></span>
                <p class="num mt-3 text-2xl font-black text-foreground">{{ $k['value'] }}</p>
                <p class="text-caption text-foreground-3">{{ $k['label'] }}</p>
            </button>
        @endforeach
    </div>

    <div class="grid gap-5 lg:grid-cols-3">
        <x-card :padded="false" class="lg:col-span-2">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <h3 class="text-subtitle">أحدث ما ينتظر قرارك</h3>
                <a href="{{ route('imperial.show', 'approvals') }}" class="text-[11px] font-black text-primary hover:underline">الكل ←</a>
            </div>
            <ul class="divide-y divide-border">
                @foreach ($latestApprovals as $i => $a)
                    <li class="anim-slide-x flex items-center gap-3 px-5 py-3.5" style="animation-delay: {{ 200 + $i * 80 }}ms">
                        <span class="num grid size-10 shrink-0 place-items-center rounded-xl bg-surface-2 text-[10px] font-black text-foreground-2" dir="ltr">{{ strtoupper($a['id']) }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-small font-black text-foreground">{{ $a['title'] ?? '' }}</p>
                            <p class="text-[10px] font-bold text-foreground-3">{{ $a['kind'] ?? '' }} · بادر {{ $a['requester'] ?? '' }}</p>
                        </div>
                        <x-badge :tone="$riskTone($a['risk'] ?? '')" size="sm">{{ $a['risk'] ?? '' }}</x-badge>
                    </li>
                @endforeach
            </ul>
        </x-card>

        <x-card>
            <h3 class="text-subtitle">إجراءات سريعة</h3>
            <div class="mt-4 grid grid-cols-2 gap-2.5">
                @foreach ($quick as $i => $q)
                    <button type="button" @click="go(@js($q['mod']), {{ $canView[$q['mod']] ? 'true' : 'false' }})" style="animation-delay: {{ 200 + $i * 60 }}ms"
                        class="anim-pop flex flex-col items-center gap-2 rounded-xl border border-border bg-surface-2/60 p-4 text-xs font-black text-foreground-2 transition-all hover:border-primary-300 hover:bg-primary-soft/60 hover:text-primary">
                        <x-i :name="$q['icon']" class="size-5 text-primary" />
                        {{ $q['label'] }}
                    </button>
                @endforeach
            </div>
        </x-card>
    </div>
</div>
