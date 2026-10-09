@php $groupsList = collect($modules)->pluck('group')->unique()->values(); @endphp
<div x-data="rbacPage(@js($matrix), {{ $overridesCount }})" class="space-y-6">
    <x-card class="border-primary-100 bg-primary-soft/50">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <h3 class="text-subtitle">مصفوفة الصلاحيات الإمبراطورية</h3>
                <p class="mt-1 text-small leading-relaxed text-foreground-2">
                    كل خانة قفل تفاعلي: <span class="font-black text-success">قفل مفتوح = مسموح</span> ·
                    <span class="font-black text-danger">قفل مقفل = ممنوع</span>. اضغط الخانة لتبديلها — يسري التغيير
                    فورًا على واجهة ذلك الدور ويُوثَّق في سجل التدقيق. جرّب تسجيل الدخول كـ
                    <span class="num font-black" dir="ltr"> fahad.zahrani@emperor.edu.sa </span> وشاهد الشريط الجانبي يتغير.
                </p>
                <div class="mt-3 flex flex-wrap items-center gap-3 text-[11px] font-black">
                    <span class="inline-flex items-center gap-1.5 text-success"><x-i name="lock-open" class="size-4" /> مسموح</span>
                    <span class="inline-flex items-center gap-1.5 text-danger"><x-i name="lock" class="size-4" /> ممنوع</span>
                    <span class="inline-flex items-center gap-1.5 text-amber-500"><x-i name="crown" class="size-4" /> الإدارة العليا: كامل الصلاحيات (غير قابلة للتخفيض)</span>
                    <span x-show="overrides > 0" x-cloak class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-amber-200/60 bg-warning-soft px-2.5 py-0.5 text-[11px] font-black text-warning"><span class="num" x-text="overrides"></span> تعديل مخصّص</span>
                </div>
            </div>
            @if ($canManage)
                <div x-show="overrides > 0" x-cloak>
                    <x-button size="sm" variant="outline" icon="refresh-ccw" @click="resetAll()">إعادة التعيين للافتراضي</x-button>
                </div>
            @endif
        </div>
    </x-card>

    @foreach ($groupsList as $g)
        <x-card :padded="false">
            <div class="border-b border-border bg-surface-2/50 px-5 py-3 text-small font-black text-foreground">{{ $g }}</div>
            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="border-b border-border font-black text-foreground-3">
                            <th class="px-4 py-2.5">الوحدة</th>
                            @foreach ($roleDefs as $r)
                                <th class="px-3 py-2.5">{{ $r['name'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (collect($modules)->where('group', $g) as $m)
                            <tr class="border-b border-border/50 last:border-0">
                                <td class="px-4 py-2.5 font-bold text-foreground-2">{{ $m['label'] }}</td>
                                @foreach ($roleDefs as $r)
                                    <td class="px-3 py-2.5">
                                        @if ($r['id'] === 'SUPER_ADMIN')
                                            <span role="img" aria-label="{{ $r['id'] }} · {{ $m['id'] }} — مسموح — مثبّت" title="الإدارة العليا: كامل الصلاحيات — غير قابلة للتخفيض من الواجهة"
                                                class="mx-auto grid size-9 cursor-not-allowed place-items-center rounded-xl border border-amber-300/60 bg-amber-100/70 text-amber-600 dark:bg-warning-soft dark:text-warning">
                                                <x-i name="crown" class="size-4" />
                                            </span>
                                        @else
                                            @php $cell = "matrix['{$r['id']}']['{$m['id']}']"; @endphp
                                            <button type="button" @click="toggle(@js($r['id']), @js($m['id']))" :aria-pressed="{{ $cell }}"
                                                :aria-label="'تبديل {{ $r['id'] }} · {{ $m['id'] }} — ' + ({{ $cell }} ? 'مسموح' : 'ممنوع')"
                                                :title="'{{ $r['id'] }} · {{ $m['id'] }} — ' + ({{ $cell }} ? 'مسموح' : 'ممنوع')"
                                                :class="{{ $cell }} ? 'border-success/40 bg-success-soft text-success hover:shadow-md hover:shadow-success/20' : 'border-danger/35 bg-danger-soft text-danger hover:shadow-md hover:shadow-danger/20'"
                                                class="mx-auto grid size-9 place-items-center rounded-xl border transition-all duration-200 hover:scale-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-500 active:scale-95">
                                                <span x-show="{{ $cell }}"><x-i name="lock-open" class="size-4" stroke-width="2.4" /></span>
                                                <span x-show="!{{ $cell }}"><x-i name="lock" class="size-4" stroke-width="2.4" /></span>
                                            </button>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endforeach

    <x-card>
        <h4 class="text-subtitle">أفعال الصلاحيات المعتمدة</h4>
        <div class="mt-3 flex flex-wrap gap-2">
            @foreach (['view', 'create', 'edit', 'delete', 'approve', 'publish', 'archive', 'import', 'export', 'manage'] as $a)
                <x-badge tone="secondary" size="sm"><span class="num" dir="ltr">{{ $a }}</span></x-badge>
            @endforeach
        </div>
    </x-card>
</div>
