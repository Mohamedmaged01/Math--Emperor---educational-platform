@php
    $categories = ['شروحات مرئية', 'تجميعات 120', 'حقائب تدريبية', 'صور أسئلة'];
    $kindTabs = ['all' => 'الكل', 'mp4' => 'ملف مرفوع (MP4)', 'youtube' => 'رابط فيديو (YouTube)', 'drive' => 'رابط فيديو (Drive)'];
    $inp = 'h-11 w-full rounded-xl border border-border bg-surface-2 px-3.5 text-small font-black text-foreground focus:border-primary-400 focus:outline-none';
@endphp
<div x-data="mediaPage(@js($media), @js($user->name))" class="space-y-5">
    <div class="flex flex-wrap items-center gap-4">
        <div class="min-w-0 flex-1">
            <h2 class="text-title text-foreground">مركز رفع وإدارة الملفات والتجميعات</h2>
            <p class="mt-1 text-small font-bold leading-relaxed text-foreground-3">رفع مذكرات التأسيس، تجميعات الـ 120 المحوسبة، ومقاطع الفيديو (رفع ملف MP4 أو إدراج رابط يوتيوب/سحابي)</p>
        </div>
        <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-secondary-100 bg-secondary-soft px-3.5 py-1.5 text-xs font-black text-secondary"><span class="num" x-text="rows.length"></span> ملف</span>
    </div>

    @if ($manage)
        <div class="grid gap-3 md:grid-cols-2">
            <x-card class="border-secondary-100 bg-secondary-soft/50 p-5!">
                <h3 class="flex items-center gap-2 text-subtitle text-foreground"><x-i name="upload-cloud" class="size-5 text-secondary" />رفع مذكرة أو ملف PDF</h3>
                <p class="mt-1 text-xs font-bold leading-relaxed text-foreground-3">PDF و DOC و PPT و XLS و PNG — يُحسب اسم الملف وحجمه تلقائيًا ويُخزَّن الملف على الخادم.</p>
                <label class="group mt-4 flex h-11 cursor-pointer items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-secondary-500 to-secondary-700 text-small font-black text-white shadow-md shadow-secondary-500/25 transition-all hover:brightness-110">
                    <x-i name="file-down" class="size-4.5" />
                    اختر ملفًا محليًا
                    <input type="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.png,.jpg,.jpeg" class="hidden" @change="onFilePicked($event, 'pdf')" />
                </label>
            </x-card>
            <x-card class="border-primary-100 bg-primary-soft/50 p-5!">
                <h3 class="flex items-center gap-2 text-subtitle text-foreground"><x-i name="play-circle" class="size-5 text-primary" />رفع / إدراج فيديو (ملف أو رابط)</h3>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    <label class="group flex h-11 cursor-pointer items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 text-small font-black text-white shadow-md shadow-primary-500/25 transition-all hover:brightness-110">
                        <x-i name="file-video" class="size-4.5" />
                        رفع ملف MP4
                        <input type="file" accept="video/*,.mp4" class="hidden" @change="onFilePicked($event, 'video')" />
                    </label>
                    <div class="flex overflow-hidden rounded-xl border border-border bg-surface shadow-xs focus-within:border-primary-400">
                        <input x-model="linkInput" @input="linkError = null" placeholder="… YouTube / رابط Drive" dir="ltr" class="h-11 min-w-0 flex-1 bg-transparent px-3 text-small font-bold focus:outline-none" />
                        <button type="button" @click="insertLink()" class="grid w-11 shrink-0 place-items-center bg-gradient-to-b from-primary-500 to-primary-700 text-white transition-all hover:brightness-110" aria-label="إدراج رابط الفيديو">
                            <x-i name="link-2" class="size-4.5" />
                        </button>
                    </div>
                </div>
                <p x-show="linkError" x-cloak role="alert" class="mt-2 text-[11px] font-bold text-danger" x-text="linkError"></p>
            </x-card>
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <div class="relative flex-1">
            <x-i name="search" class="pointer-events-none absolute inset-y-0 start-3.5 my-auto size-4.5 text-foreground-3" />
            <input x-model="q" placeholder="ابحث عن ملف أو فيديو بالعنوان، اسم المعلم، أو الملاحظات..." aria-label="ابحث عن ملف أو فيديو"
                class="h-11 w-full rounded-xl border border-border bg-surface ps-10.5 pe-4 text-small font-bold text-foreground placeholder:text-foreground-3 focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
        </div>
        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-foreground-3"><x-i name="filter" class="size-3.5" /> نوع الفيديو:</span>
            @foreach ($kindTabs as $k => $label)
                <button type="button" @click="kind = @js($k)" :aria-pressed="kind === @js($k)"
                    :class="kind === @js($k) ? 'bg-gradient-to-l from-primary-500 to-primary-700 text-white shadow-md' : 'border border-border bg-surface text-foreground-2 hover:text-primary'"
                    class="rounded-full px-3.5 py-1.5 text-[11px] font-black transition-colors">{{ $label }}</button>
            @endforeach
        </div>
        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1.5 text-[10px] font-black text-foreground-3">التصنيف:</span>
            @foreach (array_merge(['all'], $categories) as $c)
                <button type="button" @click="cat = @js($c)" :aria-pressed="cat === @js($c)"
                    :class="cat === @js($c) ? 'bg-gradient-to-l from-secondary-500 to-secondary-700 text-white shadow-md' : 'border border-border bg-surface text-foreground-2 hover:text-secondary'"
                    class="rounded-full px-3.5 py-1.5 text-[11px] font-black transition-colors">{{ $c === 'all' ? 'جميع الملفات' : $c }}</button>
            @endforeach
        </div>
    </div>

    <p x-show="playMsg" x-cloak class="flex items-center gap-2 rounded-xl border border-primary-100 bg-primary-soft/60 px-4 py-2.5 text-xs font-black text-primary">
        <x-i name="info" class="size-4 shrink-0" /><span x-text="playMsg"></span>
        <button type="button" @click="playMsg = null" aria-label="إخفاء" class="mr-auto text-primary/70 hover:text-primary"><x-i name="x" class="size-3.5" /></button>
    </p>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <template x-for="(row, i) in visible" :key="row.id">
            <article class="anim-rise group" :style="'animation-delay:' + i * 50 + 'ms'">
                <div class="flex h-full flex-col overflow-hidden rounded-2xl border border-border bg-surface shadow-sm">
                    <div class="relative flex items-end justify-between p-5 pb-3" :class="isVideo(row) ? 'bg-gradient-to-bl from-primary-700/90 via-primary-800/80 to-primary-900/80 text-white' : 'bg-surface-2/70'">
                        <template x-if="isVideo(row)">
                            <div class="contents">
                                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 80% 20%, white 0, transparent 45%)" aria-hidden="true"></div>
                                <div class="relative">
                                    <span class="inline-flex items-center whitespace-nowrap rounded-full border border-white/25 bg-white/15 px-2.5 py-0.5 text-[11px] font-black text-white backdrop-blur-sm"
                                        x-text="row.kind === 'video-mp4' ? 'ملف فيديو MP4 مرفوع' : (row.kind === 'video-yt' ? 'رابط فيديو (YouTube)' : 'رابط فيديو (Drive)')"></span>
                                    <p class="num mt-2 inline-flex items-center gap-1.5 rounded-full bg-black/25 px-2.5 py-1 text-[10px] font-black text-white/90 backdrop-blur-sm" dir="ltr">
                                        <x-i name="radio" class="size-3.5 text-secondary-100" /><span x-text="(row.duration || '—') + ' دقيقة'"></span>
                                    </p>
                                </div>
                                <span class="relative grid size-11 shrink-0 place-items-center rounded-2xl bg-white/15 text-white ring-1 ring-white/25 backdrop-blur-sm transition-transform group-hover:scale-110"><x-i name="play-circle" class="size-6" /></span>
                            </div>
                        </template>
                        <template x-if="!isVideo(row)">
                            <div class="contents">
                                <span class="inline-flex items-center whitespace-nowrap rounded-full border border-border bg-surface-2 px-2.5 py-0.5 text-[11px] font-black text-foreground-2" x-text="row.category"></span>
                                <span class="num rounded-full bg-surface px-2.5 py-1 text-[10px] font-black text-foreground-3 shadow-xs ring-1 ring-border" x-text="row.size"></span>
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-1 flex-col p-5 pt-4">
                        <span x-show="!isVideo(row)" class="inline-flex w-fit items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black" :class="categoryClass(row.category)" x-text="row.category"></span>
                        <h3 class="mt-2.5 text-small font-black leading-relaxed text-foreground" x-text="row.title"></h3>
                        <p x-show="row.description || row.notes" class="mt-1.5 line-clamp-2 text-xs font-bold leading-relaxed text-foreground-2" x-text="row.description || row.notes"></p>
                        <p class="mt-2 text-[10px] font-bold text-foreground-3" x-text="'رُفع بواسطة: ' + row.uploader + ' • ' + row.date"></p>
                        <div class="mt-1 flex items-center gap-4 text-[10px] font-black text-foreground-3">
                            <span class="num inline-flex items-center gap-1"><x-i name="download" class="size-3.5" /> <span x-text="row.downloads + ' تنزيل'"></span></span>
                        </div>
                        <div class="mt-auto flex items-center gap-2 border-t border-border pt-4">
                            <button type="button" x-show="isVideo(row)" @click="play(row)"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-3.5 py-2 text-[11px] font-black text-white shadow-md shadow-primary-500/20 transition-all hover:brightness-110">
                                <x-i name="play-circle" class="size-4" />تشغيل الفيديو
                            </button>
                            <a x-show="!isVideo(row) && row.url" :href="row.url" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-3.5 py-2 text-[11px] font-black text-foreground-2 transition-colors hover:border-primary-300 hover:text-primary">
                                <x-i name="download" class="size-3.5" /> تنزيل
                            </a>
                            <button type="button" @click="copyLink(row)" aria-label="نسخ" class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-3.5 py-2 text-[11px] font-black text-foreground-2 transition-colors hover:border-primary-300 hover:text-primary">
                                <x-i name="copy" class="size-3.5" /> نسخ
                            </button>
                            @if ($manage)
                                <button type="button" @click="startEdit(row)" aria-label="تعديل" class="inline-flex items-center gap-1.5 rounded-xl border border-border bg-surface px-3.5 py-2 text-[11px] font-black text-foreground-2 transition-colors hover:border-primary-300 hover:text-primary">
                                    <x-i name="pencil" class="size-3.5" /> تعديل
                                </button>
                                <button type="button" @click="deleting = row; reason = ''" aria-label="حذف" class="grid size-8 place-items-center rounded-xl border border-danger/25 bg-danger-soft text-danger transition-transform hover:scale-105">
                                    <x-i name="trash-2" class="size-3.5" />
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </article>
        </template>
    </div>

    {{-- edit / upload modal --}}
    <x-modal show="editing !== null" close="cancelEdit()" title-expr="editing === 'new' ? 'رفع / إدراج ملف جديد' : 'تعديل الملف'" size="lg">
        <div class="space-y-5">
            <div x-show="pending && editing === 'new'" class="flex items-start gap-3 rounded-2xl border border-secondary-100 bg-secondary-soft/60 p-4">
                <x-i name="upload-cloud" class="size-5 shrink-0 text-secondary" />
                <div>
                    <p class="text-small font-black text-foreground">تحليل الرفع جاهز</p>
                    <p class="text-[10px] font-bold text-foreground-3" x-text="(pending?.kind ?? '') + ' · ' + (pending?.size ?? '') + ' · سيُرفع الملف ويُخزَّن على الخادم عند التأكيد'"></p>
                </div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="block sm:col-span-2"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">العنوان *</span><input x-model="form.title" class="{{ $inp }}" /></label>
                <label class="block"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">التصنيف</span>
                    <select x-model="form.category" class="{{ $inp }} appearance-none">@foreach ($categories as $c)<option>{{ $c }}</option>@endforeach</select>
                </label>
                <label class="block"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">المُرفق (المعلم / القسم)</span><input x-model="form.uploader" class="{{ $inp }}" /></label>
                <label class="block"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">المدة (للفيديو)</span><input x-model="form.duration" placeholder="مثال: 24:15" dir="ltr" class="num {{ $inp }}" /></label>
                <label class="block sm:col-span-2"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">الوصف / ملاحظات</span>
                    <textarea rows="3" x-model="form.description" class="w-full rounded-xl border border-border bg-surface-2 px-3.5 py-3 text-small font-bold leading-relaxed text-foreground focus:border-primary-400 focus:outline-none"></textarea>
                </label>
                <label class="block sm:col-span-2"><span class="mb-1.5 block text-[11px] font-black text-foreground-2">رابط الفيديو (YouTube / Drive)</span><input x-model="form.url" :disabled="!!file" placeholder="https://…" dir="ltr" class="num {{ $inp }} disabled:opacity-50" /></label>
            </div>
            <p x-show="formError" x-cloak role="alert" class="rounded-xl border border-danger/25 bg-danger-soft px-3.5 py-2.5 text-xs font-bold text-danger" x-text="formError"></p>
            <div class="flex gap-2">
                <x-button full-width ::disabled="!form.title.trim() || busy" @click="saveEdit()">
                    <span x-show="busy"><x-i name="loader-2" class="size-4 animate-spin" /></span>
                    <span x-text="editing === 'new' ? 'رفع وتوثيق' : 'حفظ وتوثيق التعديل'"></span>
                </x-button>
                <x-button variant="ghost" @click="cancelEdit()">إلغاء</x-button>
            </div>
        </div>
    </x-modal>

    {{-- delete confirm --}}
    <x-modal show="deleting" close="deleting = null" title="تأكيد حذف الملف" size="sm">
        <div class="space-y-4">
            <p class="text-small font-bold leading-relaxed text-foreground-2">سيُحذف <span class="num font-black text-danger" x-text="deleting?.title"></span> نهائيًا بعد تقديم سبب — وسيُوثق في سجل التدقيق.</p>
            <input x-model="reason" placeholder="سبب الحذف (إلزامي)…" class="h-11 w-full rounded-xl border border-border bg-surface-2 px-4 text-small font-bold focus:border-danger/50 focus:outline-none" />
            <div class="flex gap-2">
                <x-button variant="danger" full-width ::disabled="!reason.trim() || busy" @click="remove()">حذف نهائي وموثق</x-button>
                <x-button variant="ghost" @click="archiveInstead()">أرشفة بدلًا من الحذف</x-button>
            </div>
        </div>
    </x-modal>
</div>
