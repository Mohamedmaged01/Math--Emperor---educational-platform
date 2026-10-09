<div x-data="approvalsPage(@js($approvals))" class="space-y-4">
    <div class="grid gap-3 sm:grid-cols-4">
        <x-card class="p-4! text-center"><p class="num text-2xl font-black text-foreground" x-text="pending.length"></p><p class="text-caption text-foreground-3">بانتظار القرار</p></x-card>
        <x-card class="p-4! text-center"><p class="num text-2xl font-black text-foreground" x-text="pending.filter(a => a.risk === 'عالٍ').length"></p><p class="text-caption text-foreground-3">عالي الخطورة</p></x-card>
        <x-card class="p-4! text-center"><p class="num text-2xl font-black text-foreground" x-text="rows.length - pending.length"></p><p class="text-caption text-foreground-3">قُرّت اليوم</p></x-card>
        <x-card class="p-4! text-center"><p class="num text-2xl font-black text-foreground">١٨ ساعة</p><p class="text-caption text-foreground-3">المتوسط الزمني</p></x-card>
    </div>

    <div x-show="pending.length === 0" x-cloak>
        <x-card class="py-14 text-center">
            <x-i name="check-circle-2" class="mx-auto size-12 text-success" />
            <h3 class="mt-4 text-title">صندوق الموافقات صافٍ</h3>
            <p class="mt-1 text-small text-foreground-3">لا توجد قرارات عالقة — خط الإنتاج المعتمد يتدفق بنجاح</p>
        </x-card>
    </div>

    <div class="space-y-3">
        <template x-for="(a, i) in pending" :key="a.id">
            <div class="anim-rise" :style="'animation-delay:' + i * 70 + 'ms'">
                <div class="flex flex-wrap items-center gap-4 rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6">
                    <span class="num grid size-12 shrink-0 place-items-center rounded-2xl bg-primary-soft text-xs font-black text-primary" dir="ltr" x-text="a.id.toUpperCase()"></span>
                    <div class="min-w-0 flex-1">
                        <h4 class="text-small font-black text-foreground" x-text="a.title"></h4>
                        <p class="mt-0.5 text-[11px] font-bold text-foreground-3" x-text="a.kind + ' · قدّمها ' + a.requester + ' · ' + a.at"></p>
                    </div>
                    <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black"
                        :class="a.risk === 'عالٍ' ? 'bg-danger-soft text-danger border-red-200/60' : 'bg-warning-soft text-warning border-amber-200/60'" x-text="'خطورة ' + a.risk"></span>
                    <div class="flex gap-2">
                        @if ($canApprove)
                            <x-button size="sm" variant="success" @click="decision = { id: a.id, type: 'approve' }">اعتماد</x-button>
                            <x-button size="sm" variant="danger" @click="decision = { id: a.id, type: 'reject' }">رفض</x-button>
                        @else
                            <x-badge size="sm">قراءة فقط لصلاحيتك</x-badge>
                        @endif
                    </div>
                </div>
            </div>
        </template>
    </div>

    <x-modal show="decision" close="decision = null; noteTxt = ''" title-expr="decision?.type === 'approve' ? 'تأكيد الاعتماد' : 'تأكيد الرفض'" size="sm">
        <div class="space-y-4">
            <p class="text-small font-bold text-foreground-2">سيُسجّل قرارك باسم <span class="font-black text-primary">{{ $user->name }}</span> مع السبب في سجل التدقيق الإمبراطوري.</p>
            <textarea rows="3" x-model="noteTxt" placeholder="سبب القرار (إلزامي للرفض)…"
                class="w-full rounded-xl border border-border bg-surface-2 px-4 py-3 text-small font-bold focus:border-primary-400 focus:outline-none"></textarea>
            <button type="button" @click="decide()" :disabled="busy || (decision?.type === 'reject' && !noteTxt.trim())"
                :class="decision?.type === 'approve' ? 'bg-success-soft text-success border-success/20' : 'bg-danger-soft text-danger border-danger/20'"
                class="relative inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border px-6 text-sm font-black transition-all duration-200 hover:brightness-95 disabled:pointer-events-none disabled:opacity-55">
                تأكيد القرار نهائيًا
            </button>
        </div>
    </x-modal>
</div>
