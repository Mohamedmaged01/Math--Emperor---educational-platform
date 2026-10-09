@php $capPlans = ['باقة ملكية VIP', 'باقة ذهبية', 'باقة مجانية']; @endphp
<div x-data="capsPage(@js($students))" class="space-y-5">
    <div class="flex flex-wrap items-center gap-4">
        <div class="min-w-0 flex-1">
            <h2 class="text-title text-foreground">إدارة صلاحيات الطلاب وسقف النشاط المصرح به</h2>
            <p class="mt-1 text-small font-bold leading-relaxed text-foreground-3">تحديد كمية النشاط المسموح للطالب استهلاكه (عدد جلسات المحاكي، إتاحة تجميعات الـ 120، قاعات البث، والمعلم الذكي)</p>
        </div>
        <div class="flex items-center gap-2.5">
            <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-primary-100 bg-primary-soft px-3.5 py-1.5 text-xs font-black text-primary"><span class="num" x-text="rows.length"></span> طالب</span>
            <span class="inline-flex items-center whitespace-nowrap rounded-full border border-success-100 bg-success-soft px-2.5 py-0.5 text-[11px] font-black text-success" x-text="active + ' مفعل'"></span>
            <span class="inline-flex items-center whitespace-nowrap rounded-full border border-red-200/60 bg-danger-soft px-2.5 py-0.5 text-[11px] font-black text-danger" x-text="(rows.length - active) + ' معلق'"></span>
        </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
        <div class="relative flex-1">
            <x-i name="search" class="pointer-events-none absolute inset-y-0 start-3.5 my-auto size-4.5 text-foreground-3" />
            <input x-model="q" placeholder="ابحث بالاسم أو البريد أو الجوال..." aria-label="ابحث بالاسم أو البريد أو الجوال"
                class="h-11 w-full rounded-xl border border-border bg-surface ps-10.5 pe-4 text-small font-bold text-foreground placeholder:text-foreground-3 focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach (array_merge(['all'], $capPlans) as $p)
                <button type="button" @click="plan = @js($p)" :aria-pressed="plan === @js($p)"
                    :class="plan === @js($p) ? 'bg-gradient-to-l from-primary-500 to-primary-700 text-white shadow-md shadow-primary-500/25' : 'border border-border bg-surface text-foreground-2 hover:border-primary-200 hover:text-primary'"
                    class="rounded-full px-4 py-2 text-xs font-black transition-all duration-200">{{ $p === 'all' ? 'جميع باقات الطلاب' : $p }}</button>
            @endforeach
        </div>
    </div>

    <div x-show="visible.length === 0" x-cloak>
        <x-card class="py-12 text-center">
            <x-i name="graduation-cap" class="mx-auto size-10 text-foreground-3" />
            <p class="mt-3 text-subtitle text-foreground">لا طلاب مطابقين للتصفية الحالية</p>
        </x-card>
    </div>

    <template x-for="(s, i) in visible" :key="s.id">
        <div class="anim-rise" :style="'animation-delay:' + i * 60 + 'ms'">
            <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm">
                <div class="flex flex-wrap items-center gap-3 border-b border-border bg-surface-2/60 px-5 py-4">
                    <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-primary-400 to-primary-700 text-lg font-black text-white" x-text="String(s.name).charAt(0)"></span>
                    <div class="min-w-0 flex-1">
                        <h3 class="truncate text-subtitle text-foreground" x-text="s.name"></h3>
                        <p class="num inline-flex items-center gap-1.5 text-[10px] font-bold text-foreground-3" dir="ltr"><x-i name="mail" class="size-3" /> <span x-text="s.email"></span></p>
                    </div>
                    <span class="inline-flex items-center whitespace-nowrap rounded-full border px-3.5 py-1.5 text-xs font-black" :class="planClass(s.plan)" x-text="s.plan"></span>
                    <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black"
                        :class="s.status === 'نشط' ? 'bg-success-soft text-success border-success-100' : 'bg-danger-soft text-danger border-red-200/60'" x-text="s.status === 'نشط' ? 'الحساب مفعل' : 'الحساب معلق'"></span>
                </div>

                <div class="grid gap-4 p-5 lg:grid-cols-2">
                    <div class="space-y-2.5 text-xs font-bold text-foreground-2">
                        <p class="flex items-center gap-2"><x-i name="mail" class="size-4 text-primary" /><span class="font-black text-foreground-3">البريد:</span> <span class="num truncate" dir="ltr" x-text="s.email"></span></p>
                        <p class="flex items-center gap-2"><x-i name="phone" class="size-4 text-primary" /><span class="font-black text-foreground-3">الجوال:</span> <span class="num" dir="ltr" x-text="s.phone"></span></p>
                        <p class="flex items-center gap-2"><x-i name="graduation-cap" class="size-4 text-primary" /><span class="font-black text-foreground-3">المرحلة:</span> <span x-text="s.grade"></span></p>
                        <p class="flex items-center gap-2"><x-i name="calendar-range" class="size-4 text-primary" /><span class="font-black text-foreground-3">تاريخ الانضمام:</span> <span class="num" x-text="s.enrolled"></span></p>
                    </div>
                    <div class="grid grid-cols-2 gap-2" aria-label="سقف النشاط">
                        @foreach ([['gauge', 'سقف المحاكي', 'simCap(s)'], ['boxes', 'خزينة الـ 120', "String(s.vault ?? '—')"], ['radio', 'البث المباشر', "String(s.live ?? '—')"], ['bot', 'المعلم AI', 'aiCap(s)']] as [$icon, $label, $expr])
                            <div class="rounded-xl border border-border bg-surface-2/60 p-3">
                                <p class="flex items-center gap-1.5 text-[10px] font-black text-foreground-3"><x-i :name="$icon" class="size-3.5 text-primary" />{{ $label }}</p>
                                <p class="num mt-1.5 text-xs font-black" :class="capTone({{ $expr }})" x-text="{{ $expr }}"></p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($manage)
                    <div class="flex flex-wrap gap-2 border-t border-border px-5 py-4">
                        <button type="button" @click="toggleSuspend(s)"
                            :class="s.status === 'نشط' ? 'bg-danger-soft text-danger border-danger/20' : 'bg-success-soft text-success border-success/20'"
                            class="relative inline-flex h-9 items-center justify-center gap-2 rounded-xl border px-4 text-sm font-black transition-all duration-200 hover:brightness-95 active:scale-[0.985]">
                            <span x-show="s.status === 'نشط'"><x-i name="ban" class="size-4" /></span>
                            <span x-show="s.status !== 'نشط'"><x-i name="power" class="size-4" /></span>
                            <span x-text="s.status === 'نشط' ? 'تعليق الحساب' : 'تفعيل الحساب'"></span>
                        </button>
                        <x-button size="sm" variant="outline" icon="sliders-horizontal" @click="openEditor(s)">تعديل الصلاحيات والسقف</x-button>
                    </div>
                @endif
            </div>
        </div>
    </template>

    <x-modal show="edited" close="edited = null" title="تعديل الصلاحيات والسقف" size="lg">
        <template x-if="edited">
            <div class="space-y-5">
                <div class="flex items-center gap-3 rounded-2xl border border-primary-100 bg-primary-soft/60 p-4">
                    <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-surface text-lg font-black text-primary shadow-xs" x-text="String(edited.name).charAt(0)"></span>
                    <div class="min-w-0">
                        <p class="truncate text-subtitle text-foreground" x-text="edited.name"></p>
                        <p class="num truncate text-[11px] font-bold text-foreground-3" dir="ltr" x-text="edited.email"></p>
                    </div>
                    <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black" :class="planClass(edited.plan)" x-text="edited.plan"></span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-border bg-surface-2/50 p-4 sm:col-span-2">
                        <p class="mb-3 flex items-center gap-2 text-small font-black text-foreground"><x-i name="gauge" class="size-4.5 text-primary" />سقف المحاكي الشهري</p>
                        <div class="grid gap-3 sm:grid-cols-[1fr_auto]">
                            <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-border bg-surface px-3.5 py-2.5">
                                <input type="checkbox" x-model="form.simUnlimited" class="size-4 accent-primary-600" />
                                <span class="text-xs font-black text-foreground-2">مفتوح غير محدود ∞</span>
                            </label>
                            <label class="block">
                                <span class="mb-1 block text-[10px] font-black text-foreground-3">الحد الأقصى للجلسات/الشهر</span>
                                <input type="number" min="0" :disabled="form.simUnlimited" x-model="form.simsLimit" :placeholder="form.simUnlimited ? '∞' : 'مثال: 25'" dir="ltr"
                                    class="num h-11 w-full rounded-xl border border-border bg-surface px-3.5 text-small font-black text-foreground focus:border-primary-400 focus:outline-none disabled:opacity-50 sm:w-48" />
                            </label>
                        </div>
                    </div>
                    @foreach ([['vault', 'boxes', 'text-secondary', 'خزينة الـ 120 PDF'], ['live', 'radio', 'text-rose-500', 'قاعات البث المباشر']] as [$k, $icon, $tone, $label])
                        <label class="block rounded-2xl border border-border bg-surface-2/50 p-4">
                            <p class="mb-3 flex items-center gap-2 text-small font-black text-foreground"><x-i :name="$icon" class="size-4.5 {{ $tone }}" />{{ $label }}</p>
                            <select x-model="form.{{ $k }}" class="h-11 w-full appearance-none rounded-xl border border-border bg-surface px-3.5 text-small font-black text-foreground focus:border-primary-400 focus:outline-none">
                                @foreach (['متاح', 'مقيد', 'محظور'] as $o)<option>{{ $o }}</option>@endforeach
                            </select>
                        </label>
                    @endforeach
                    <label class="block rounded-2xl border border-border bg-surface-2/50 p-4 sm:col-span-2">
                        <p class="mb-3 flex items-center gap-2 text-small font-black text-foreground"><x-i name="bot" class="size-4.5 text-accent" />المعلم الذكي AI — الاستشارات اليومية (0 = معطل)</p>
                        <input type="number" min="0" x-model="form.aiLimit" dir="ltr" class="num h-11 w-full rounded-xl border border-border bg-surface px-3.5 text-small font-black text-foreground focus:border-primary-400 focus:outline-none sm:w-48" />
                    </label>
                </div>

                <div class="flex gap-2">
                    <x-button full-width icon="shield-check" @click="saveEditor()" ::disabled="busy">حفظ وتوثيق التعديلات</x-button>
                    <x-button variant="ghost" @click="edited = null">إلغاء</x-button>
                </div>
            </div>
        </template>
    </x-modal>
</div>
