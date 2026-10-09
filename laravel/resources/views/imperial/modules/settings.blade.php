@php
    $fields = ['platformName' => 'اسم المنصة', 'simTimePerQuestion' => 'زمن السؤال الرسمي', 'weeklyGoalDefault' => 'هدف الأسبوع الافتراضي', 'sessionHours' => 'مدة الجلسة', 'auditRetention' => 'احتفاظ التدقيق'];
@endphp
<div x-data="settingsPage" class="mx-auto max-w-2xl space-y-5">
    <x-card>
        <h3 class="text-subtitle">التهيئة المركزية للمنصة</h3>
        <p class="mt-1 text-small text-foreground-3">القيم إرشادية لتجربة التهيئة — لا تُعرض أسرار أو مفاتيح هنا، ولا يجب أن تُعرض أبدًا.</p>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            @foreach ($fields as $k => $label)
                <label class="block">
                    <span class="mb-1.5 block text-xs font-black text-foreground-2">{{ $label }}</span>
                    <input x-model="cfg.{{ $k }}" @disabled(! $canEdit)
                        class="h-11 w-full rounded-xl border border-border bg-surface-2 px-4 text-small font-black text-foreground focus:border-primary-400 focus:outline-none" />
                </label>
            @endforeach
        </div>
        <x-button class="mt-6" full-width ::disabled="{{ $canEdit ? 'false' : 'true' }} || saved || busy" @click="save()">
            <span x-text="saved ? 'تم الحفظ وتوثيقه ✓' : 'حفظ التهيئة'"></span>
        </x-button>
    </x-card>

    <x-card>
        <h4 class="text-subtitle">إحصاءات مساحة العمل</h4>
        <div class="mt-3 grid grid-cols-3 gap-2 text-center">
            @foreach ([['سجلات مخزنة', $workspaceStats['records']], ['أحداث تدقيق', $workspaceStats['audit']], ['موافقات معلقة', $workspaceStats['pending']]] as [$l, $v])
                <div class="rounded-xl bg-surface-2 p-3">
                    <p class="num text-lg font-black text-foreground">{{ $v }}</p>
                    <p class="text-[10px] font-bold text-foreground-3">{{ $l }}</p>
                </div>
            @endforeach
        </div>
    </x-card>
</div>
