@php
    $placements = [
        ['key' => 'HOME_BOTTOM', 'label' => 'الصفحة الرئيسية — أسفل المحتوى', 'desc' => 'مربع إعلاني واحد بعد CTA النهائية (بعد كل التحفيز)'],
        ['key' => 'PUBLIC_CONTENT_TOP', 'label' => 'المحتوى العام — أعلى (دورات / شروحات / صفحات أدوات)', 'desc' => 'داخل صفحات المحتوى العام إذ إنها ليست مناطق تعليمية حساسة'],
        ['key' => 'PUBLIC_CONTENT_BOTTOM', 'label' => 'المحتوى العام — أسفل (دورات / شروحات)', 'desc' => 'أسفل المحتوى العام — موضع آمن لا يتعارض مع التنقل أو الوظائف'],
    ];
    $privacy = [
        'لا إعلانات داخل الاختبارات أو الجلسات (المحاكي وبوابات الطلاب)',
        'لا إعلانات داخل لوحة الإدارة أو بوابة الطالب أو بوابة المعلم',
        'الإعلانات لا تتعارض أبدًا مع أزرار التنقل أو الوظائف — إعلانات منفصلة واضحة التسمية مسبقًا',
        'لا نقرات صناعية، لا مرات ظهور وهمية، ولا تحفيزات مصطنعة',
    ];
@endphp
<div x-data="marketingPage(@js($marketing))" class="space-y-6">
    <div class="min-w-0">
        <h2 class="text-title text-foreground">التسويق والإعلانات — Google AdSense Ready</h2>
        <p class="mt-1 text-small font-bold leading-relaxed text-foreground-3">تكامل مركزي آمن: معرف الناشر يُتحقق منه ويُحفظ على الخادم، ولا يُحمَّل سكربت AdSense إلا عند التفعيل بمعرف صالح خارج وضع الاختبار.</p>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        <x-card>
            <div class="flex items-center gap-3">
                <span class="grid size-12 place-items-center rounded-2xl bg-gradient-to-br from-amber-100 to-amber-200 text-amber-600 shadow-xs dark:from-warning-soft dark:to-warning-soft dark:text-warning"><x-i name="megaphone" class="size-6" /></span>
                <div>
                    <h3 class="text-subtitle">Google AdSense — التكوين المركزي</h3>
                    <p class="text-[10px] font-black text-foreground-3">معرف الناشر يُحفظ فورًا ويُطبَّق على الصفحة الرئيسية</p>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                <label class="block">
                    <span class="mb-1.5 flex items-center justify-between text-[11px] font-black text-foreground-2">
                        <span>معرف الناشر — Publisher ID (منصة AdSense فقط)</span>
                        <span class="num text-[9px] font-black text-foreground-3" dir="ltr">ca-pub-XXXXXXXXXXXXXXXX</span>
                    </span>
                    <div class="flex gap-2">
                        <input x-model="pub" @input="error = null" dir="ltr" @disabled(! $canManage) placeholder="ca-pub-XXXXXXXXXXXXXXXX"
                            :class="error ? 'border-danger/50 focus:border-danger/60' : 'focus:border-primary-400'"
                            class="num h-11 min-w-0 flex-1 rounded-xl border border-border bg-surface-2 px-3.5 text-small font-black transition-colors focus:outline-none" />
                        <x-button class="shrink-0" ::disabled="{{ $canManage ? 'false' : 'true' }} || saved" @click="applyPub()"><span x-text="saved ? '✓' : 'حفظ'"></span></x-button>
                    </div>
                    <p x-show="error" x-cloak role="alert" class="mt-2 flex items-center gap-1.5 text-[11px] font-bold text-danger"><x-i name="alert-circle" class="size-3.5" /><span x-text="error"></span></p>
                    <p x-show="saved" x-cloak role="status" class="mt-2 flex items-center gap-1.5 text-[11px] font-bold text-success"><x-i name="check-circle-2" class="size-3.5" /> حُفظ المعرف (audit recorded)</p>
                </label>

                <div class="grid gap-2.5">
                    @foreach ([['adsenseEnabled', 'تفعيل AdSense (معطل افتراضيًا)'], ['autoAds', 'الإعلانات التلقائية Auto Ads'], ['testMode', 'وضع الاختبار — بلا إعلانات حقيقية إطلاقًا']] as [$k, $label])
                        <label class="flex cursor-pointer items-center justify-between rounded-xl border border-border bg-surface-2/70 px-3.5 py-2.5">
                            <span class="text-xs font-black text-foreground-2">{{ $label }}</span>
                            <input type="checkbox" @disabled(! $canManage) :checked="m.{{ $k }}" @change="save({ {{ $k }}: $event.target.checked })" class="size-4.5 accent-primary-600" />
                        </label>
                    @endforeach
                </div>

                <div class="grid gap-2">
                    <div class="flex items-center justify-between rounded-xl border border-border bg-surface-2/50 px-3.5 py-2.5">
                        <p class="text-[10px] font-black text-foreground-3">حالة AdSense</p>
                        <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black" :class="m.adsenseEnabled && valid(m.publisherId) ? 'bg-success-soft text-success border-success-100' : 'bg-warning-soft text-warning border-amber-200/60'"
                            x-text="m.adsenseEnabled && valid(m.publisherId) ? 'مفعّل ويعمل' : (m.adsenseEnabled ? 'مفعّل لكن ID غير صالح أو مفقود — معطل تلقائيًا' : 'معطل (افتراضي)')"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-border bg-surface-2/50 px-3.5 py-2.5">
                        <p class="text-[10px] font-black text-foreground-3">وضع الاختبار</p>
                        <span class="inline-flex items-center whitespace-nowrap rounded-full border px-2.5 py-0.5 text-[11px] font-black" :class="m.testMode ? 'bg-warning-soft text-warning border-amber-200/60' : 'bg-surface-2 text-foreground-2 border-border'"
                            x-text="m.testMode ? 'مفعّل — لا يُحمَّل أي إعلان حقيقي' : 'معطل'"></span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-border bg-surface-2/50 px-3.5 py-2.5">
                        <p class="text-[10px] font-black text-foreground-3">البيئة الإنتاجية</p>
                        <x-badge size="sm">تحكم الإدارة صراحةً</x-badge>
                    </div>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center gap-3">
                <span class="grid size-12 place-items-center rounded-2xl bg-primary-soft text-primary shadow-xs"><x-i name="globe-2" class="size-6" /></span>
                <div>
                    <h3 class="text-subtitle">مدير مواضع الإعلانات — Ad Placement Manager</h3>
                    <p class="text-[10px] font-black text-foreground-3">مواضع غير مزعجة ومحدودة العدد</p>
                </div>
            </div>
            <div class="mt-5 space-y-3">
                @foreach ($placements as $p)
                    <div class="flex items-center justify-between gap-3 rounded-xl border border-border bg-surface-2/50 p-3.5">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-xs font-black text-foreground">{{ $p['label'] }}</p>
                            <p class="mt-0.5 text-[10px] font-bold leading-relaxed text-foreground-3">{{ $p['desc'] }}</p>
                        </div>
                        <button type="button" @disabled(! $canManage) @click="togglePlacement(@js($p['key']))" :aria-pressed="placementOn(@js($p['key']))"
                            :aria-label="(placementOn(@js($p['key'])) ? 'تعطيل' : 'تفعيل') + ' موضع {{ $p['label'] }}'"
                            :class="placementOn(@js($p['key'])) ? 'border-success/40 bg-success-soft text-success' : 'border-border bg-surface text-foreground-3'"
                            class="grid size-9 shrink-0 place-items-center rounded-xl border transition-colors">
                            <span x-show="placementOn(@js($p['key']))"><x-i name="check-circle-2" class="size-4.5" /></span>
                            <span x-show="!placementOn(@js($p['key']))"><x-i name="alert-circle" class="size-4.5" /></span>
                        </button>
                    </div>
                @endforeach
            </div>
            <p class="mt-4 flex items-center gap-2 rounded-xl border border-secondary-100 bg-secondary-soft/60 p-3.5 text-[10px] font-bold text-foreground-2">
                <x-i name="shield-check" class="size-4 shrink-0 text-secondary" />
                أقصى عدد إعلانات بالصفحة: <span class="num font-black text-secondary" x-text="m.maxAdsPerPage"></span> — حد أعلى ٣ للحفاظ على تجربة الطالب
            </p>
            <input type="number" min="0" max="3" @disabled(! $canManage) :value="m.maxAdsPerPage" @change="save({ maxAdsPerPage: Math.max(0, Math.min(3, Number($event.target.value) || 0)) })"
                class="num mt-2 h-10 w-full rounded-xl border border-border bg-surface-2 px-3.5 text-small font-black text-foreground focus:border-primary-400 focus:outline-none" />
        </x-card>
    </div>

    <x-card class="border-amber-200/60 bg-warning-soft/50">
        <div class="flex items-start gap-3">
            <x-i name="alert-circle" class="mt-0.5 size-5 shrink-0 text-warning" />
            <div class="space-y-2">
                <h3 class="text-subtitle text-foreground">سياسة حماية الطالب والبيئة</h3>
                <ul class="space-y-1.5">
                    @foreach ($privacy as $i => $p)
                        <li class="flex items-start gap-2 text-xs font-bold leading-relaxed text-foreground-2">
                            <span class="num mt-1 grid size-5 shrink-0 place-items-center rounded-full bg-amber-100 text-[9px] font-black text-warning dark:bg-warning-soft" dir="ltr">{{ $i + 1 }}</span>
                            {{ $p }}
                        </li>
                    @endforeach
                </ul>
                <p class="mt-3 text-[10px] font-black text-warning" x-text="m.policy"></p>
            </div>
        </div>
    </x-card>
</div>
