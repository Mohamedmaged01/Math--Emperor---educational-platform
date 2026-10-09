{{-- generic dataset table: search · sort · paging · select · bulk · CRUD · import/export --}}
<div x-data="dataTable(@js($table))" class="space-y-4">
    {{-- toolbar --}}
    <div class="flex flex-wrap items-center gap-2">
        <div class="relative min-w-52 flex-1">
            <x-i name="search" class="pointer-events-none absolute inset-y-0 start-3.5 my-auto size-4 text-foreground-3" />
            <input x-model="q" @input="page = 0" :placeholder="'ابحث في ' + title + '…'" :aria-label="'بحث ' + title"
                class="h-10.5 w-full rounded-xl border border-border bg-surface ps-10 pe-3 text-small font-bold text-foreground placeholder:text-foreground-3 focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
        </div>
        <template x-if="perms.create">
            <x-button size="sm" icon="pencil" @click="beginForm(null)">إضافة سجل</x-button>
        </template>
        <template x-if="perms.import">
            <label class="inline-flex h-9 cursor-pointer items-center gap-1.5 rounded-xl border border-border-strong bg-surface px-4 text-sm font-black text-foreground transition-colors hover:border-primary-300 hover:text-primary">
                <x-i name="upload-cloud" class="size-4" /> استيراد CSV
                <input type="file" accept=".csv" class="hidden" @change="onImportFile($event)" />
            </label>
        </template>
        <template x-if="perms.export">
            <div class="flex overflow-hidden rounded-xl border border-border-strong">
                <template x-for="fmt in ['csv', 'json']" :key="fmt">
                    <button type="button" @click="exportRows(fmt)" class="bg-surface px-3 py-2 text-[11px] font-black text-foreground-2 transition-colors hover:bg-surface-2 hover:text-primary">
                        <span class="inline-flex items-center gap-1"><x-i name="download" class="size-3.5" /> <span x-text="fmt.toUpperCase()"></span></span>
                    </button>
                </template>
            </div>
        </template>
        <template x-if="!perms.create && !perms.export && !perms.edit">
            <x-badge size="sm" icon="shield-alert">صلاحية قراءة فقط</x-badge>
        </template>
    </div>

    <template x-if="scope">
        <p class="rounded-xl border border-accent-100 bg-accent-soft/70 px-4 py-2.5 text-[11px] font-black text-accent" x-text="'نطاق صلاحياتك: ' + scope"></p>
    </template>

    {{-- bulk bar --}}
    <div x-show="selected.length > 0 && perms.archive" x-cloak x-transition
        class="flex items-center gap-3 rounded-xl border border-primary-200 bg-primary-soft/70 px-4 py-2.5">
        <span class="num text-xs font-black text-primary" x-text="selected.length + ' محدد'"></span>
        <button type="button" @click="bulk('archive')" class="rounded-lg bg-surface px-3 py-1.5 text-[11px] font-black text-foreground shadow-xs transition-colors hover:text-primary">أرشفة جماعية</button>
        <button type="button" @click="bulk('publish')" class="rounded-lg bg-surface px-3 py-1.5 text-[11px] font-black text-foreground shadow-xs transition-colors hover:text-success">نشر جماعي</button>
        <button type="button" @click="selected = []" class="mr-auto text-primary" aria-label="إلغاء التحديد"><x-i name="x" class="size-4" /></button>
    </div>

    {{-- table --}}
    <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-right text-small">
                <thead>
                    <tr class="border-b border-border bg-surface-2/70 text-[11px] font-black text-foreground-3">
                        <th class="w-10 px-3 py-3">
                            <input type="checkbox" aria-label="تحديد الكل" :checked="allChecked" @change="toggleAll($event.target.checked)" class="size-4 accent-primary-600" />
                        </th>
                        <template x-for="c in columns" :key="c.key">
                            <th class="px-3 py-3">
                                <button type="button" @click="sortBy(c.key)" class="inline-flex items-center gap-1 hover:text-primary">
                                    <span x-text="c.label"></span>
                                    <span x-show="sortKey === c.key" class="transition-transform" :class="sortDir === -1 && 'rotate-180'"><x-i name="chevron-down" class="size-3.5" /></span>
                                </button>
                            </th>
                        </template>
                        <th class="px-3 py-3">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr x-show="pageRows.length === 0">
                        <td :colspan="columns.length + 2" class="px-4 py-12 text-center">
                            <x-i name="inbox" class="mx-auto size-8 text-foreground-3" />
                            <p class="mt-2 font-black text-foreground-2">لا نتائج</p>
                            <p class="text-xs font-bold text-foreground-3">جرّب مصطلحًا مختلفًا</p>
                        </td>
                    </tr>
                    <template x-for="(row, i) in pageRows" :key="row.id">
                        <tr class="anim-rise border-b border-border/60 transition-colors last:border-0 hover:bg-surface-2/50" :style="'animation-delay:' + i * 30 + 'ms'"
                            :class="selected.includes(String(row.id)) && 'bg-primary-soft/40'">
                            <td class="px-3 py-2.5">
                                <input type="checkbox" aria-label="تحديد" :checked="selected.includes(String(row.id))" @change="toggleRow(row, $event.target.checked)" class="size-4 accent-primary-600" />
                            </td>
                            <template x-for="c in columns" :key="c.key">
                                <td class="max-w-56 px-3 py-2.5 font-bold text-foreground-2">
                                    <template x-if="c.badge">
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black" :class="chipClass(row[c.key])" x-text="String(row[c.key] ?? '')"></span>
                                    </template>
                                    <template x-if="!c.badge"><span class="block truncate" x-text="String(row[c.key] ?? '')"></span></template>
                                </td>
                            </template>
                            <td class="px-3 py-2.5">
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="viewing = row" aria-label="عرض" title="عرض" class="grid size-8 place-items-center rounded-lg text-foreground-3 transition-all hover:scale-110 hover:bg-surface-2 hover:text-primary"><x-i name="eye" class="size-4" /></button>
                                    <template x-if="perms.edit">
                                        <button type="button" @click="beginForm(row)" aria-label="تعديل" title="تعديل" class="grid size-8 place-items-center rounded-lg text-foreground-3 transition-all hover:scale-110 hover:bg-surface-2 hover:text-primary"><x-i name="pencil" class="size-4" /></button>
                                    </template>
                                    <template x-if="dataset === 'stats' && perms.edit">
                                        <button type="button" @click="toggleStat(row)" :aria-label="isOn(row.enabled) ? 'إخفاء من الرئيسية' : 'إظهار في الرئيسية'" :title="isOn(row.enabled) ? 'إخفاء من الرئيسية' : 'إظهار في الرئيسية'"
                                            :class="isOn(row.enabled) ? 'text-warning hover:bg-warning-soft' : 'text-success hover:bg-success-soft'" class="grid size-8 place-items-center rounded-lg transition-all hover:scale-110">
                                            <span x-show="isOn(row.enabled)"><x-i name="eye-off" class="size-4" /></span>
                                            <span x-show="!isOn(row.enabled)"><x-i name="eye" class="size-4" /></span>
                                        </button>
                                    </template>
                                    <template x-if="perms.publish && String(row.status ?? '').includes('مراجعة')">
                                        <button type="button" @click="transition(row, 'publish', 'نشر')" aria-label="نشر" title="نشر" class="grid size-8 place-items-center rounded-lg text-success transition-all hover:scale-110 hover:bg-success-soft"><x-i name="check-circle-2" class="size-4" /></button>
                                    </template>
                                    <template x-if="perms.archive && 'status' in row && String(row.status ?? '') !== 'مؤرشف'">
                                        <button type="button" @click="transition(row, 'archive', 'أرشفة')" aria-label="أرشفة" title="أرشفة" class="grid size-8 place-items-center rounded-lg text-warning transition-all hover:scale-110 hover:bg-warning-soft"><x-i name="archive" class="size-4" /></button>
                                    </template>
                                    <template x-if="perms.delete">
                                        <button type="button" @click="deleting = row; reason = ''" aria-label="حذف" title="حذف" class="grid size-8 place-items-center rounded-lg text-danger transition-all hover:scale-110 hover:bg-danger-soft"><x-i name="trash-2" class="size-4" /></button>
                                    </template>
                                    <template x-if="!perms.edit && !perms.delete && dataset !== 'audit'">
                                        <button type="button" @click="denied('edit')" aria-label="فعل منع الصلاحية (يُسجل)" title="فعل منع الصلاحية (يُسجل)" class="grid size-8 place-items-center rounded-lg text-foreground-3 transition-all hover:scale-110 hover:bg-surface-2"><x-i name="shield-alert" class="size-4" /></button>
                                    </template>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- pagination --}}
        <div class="flex items-center justify-between border-t border-border px-4 py-2.5 text-[11px] font-black text-foreground-3">
            <span class="num" x-text="filtered.length + ' سجل · صفحة ' + (page + 1) + ' من ' + pages"></span>
            <div class="flex gap-1">
                <button type="button" :disabled="page === 0" @click="page--" class="grid size-8 place-items-center rounded-lg border border-border disabled:opacity-40" aria-label="السابق"><x-i name="chevrons-right" class="size-4" /></button>
                <button type="button" :disabled="page >= pages - 1" @click="page++" class="grid size-8 place-items-center rounded-lg border border-border disabled:opacity-40" aria-label="التالي"><x-i name="chevrons-left" class="size-4" /></button>
            </div>
        </div>
    </div>

    {{-- view modal --}}
    <x-modal show="viewing" close="viewing = null" title="تفاصيل السجل" size="sm">
        <dl class="space-y-2">
            <template x-for="[k, v] in Object.entries(viewing ?? {})" :key="k">
                <div class="flex items-start justify-between gap-3 border-b border-border/60 pb-2 text-small">
                    <dt class="num font-black text-foreground-3" dir="ltr" x-text="k"></dt>
                    <dd class="text-left font-bold text-foreground" x-text="String(v)"></dd>
                </div>
            </template>
        </dl>
    </x-modal>

    {{-- add/edit modal --}}
    <x-modal show="adding" close="adding = false; editId = null" title-expr="editId ? 'تعديل السجل' : 'إضافة سجل جديد'" size="md">
        <form @submit.prevent="submitForm()" class="grid gap-3 sm:grid-cols-2">
            <template x-for="c in columns" :key="c.key">
                <label class="block">
                    <span class="mb-1 block text-[11px] font-black text-foreground-2" x-text="c.label"></span>
                    <input x-model="formVals[c.key]" class="h-10 w-full rounded-xl border border-border bg-surface-2 px-3 text-small font-bold text-foreground focus:border-primary-400 focus:outline-none" />
                </label>
            </template>
            <div class="mt-2 flex gap-2 sm:col-span-2">
                <x-button type="submit" full-width ::disabled="busy"><span x-text="editId ? 'حفظ التعديلات' : 'إضافة للسجلات'"></span></x-button>
                <x-button variant="ghost" @click="adding = false; editId = null">إلغاء</x-button>
            </div>
        </form>
    </x-modal>

    {{-- delete confirm --}}
    <x-modal show="deleting" close="deleting = null" title="تأكيد الحذف" size="sm">
        <div class="space-y-4">
            <p class="text-small font-bold leading-relaxed text-foreground-2">
                سيُحذف السجل <span class="num font-black text-danger" x-text="deleting?.id"></span> نهائيًا بعد تقديم سبب — وسيُوثق في سجل التدقيق. للبيانات الحساسة يُفضَّل الأرشفة.
            </p>
            <input x-model="reason" placeholder="سبب الحذف (إلزامي)…" class="h-11 w-full rounded-xl border border-border bg-surface-2 px-4 text-small font-bold focus:border-danger/50 focus:outline-none" />
            <div class="flex gap-2">
                <x-button variant="danger" full-width ::disabled="!reason.trim() || busy" @click="remove()">حذف نهائي وموثق</x-button>
                <x-button variant="ghost" @click="archiveInstead()">أرشفة بدلًا منه</x-button>
            </div>
        </div>
    </x-modal>

    {{-- import preview --}}
    <x-modal show="importing" close="importing = null" title="معاينة الاستيراد — راجع قبل الالتزام" size="lg">
        <div class="space-y-4">
            <p x-show="importing && importing.length === 0" class="rounded-xl border border-danger/25 bg-danger-soft px-4 py-3 text-small font-bold text-danger">
                الملف فارغ أو غير مطابق لتنسيق CSV (صف عناوين + صفوف بيانات). لم يُقرأ أي سجل.
            </p>
            <template x-if="importing && importing.length > 0">
                <div class="space-y-4">
                    <p class="text-small font-bold text-foreground-2">تم تحليل <span class="num font-black text-primary" x-text="importing.length"></span> سجلًا. لن يُدرج شيء قبل تأكيدك الصريح.</p>
                    <div class="max-h-56 overflow-auto rounded-xl border border-border">
                        <table class="w-full text-right text-xs">
                            <thead class="sticky top-0 bg-surface-2 font-black text-foreground-3">
                                <tr><template x-for="k in Object.keys(importing[0])" :key="k"><th class="num px-3 py-2" dir="ltr" x-text="k"></th></template></tr>
                            </thead>
                            <tbody>
                                <template x-for="(r, ri) in importing.slice(0, 8)" :key="ri">
                                    <tr class="border-t border-border/60 font-bold text-foreground-2">
                                        <template x-for="(v, vi) in Object.values(r)" :key="vi"><td class="truncate px-3 py-2" x-text="String(v)"></td></template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
            <div class="flex gap-2">
                <x-button full-width ::disabled="!importing || importing.length === 0 || busy" @click="commitImport()"><span x-text="'تأكيد واستيراد (' + (importing?.length ?? 0) + ' سجل)'"></span></x-button>
                <x-button variant="ghost" @click="importing = null">إلغاء</x-button>
            </div>
        </div>
    </x-modal>
</div>
