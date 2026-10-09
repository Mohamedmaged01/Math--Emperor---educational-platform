@php
    $pillars = [
        ['icon' => 'shield-check', 'title' => 'علمية نابعة من الواقع', 'desc' => 'نحلل آلاف نتائج الاختبارات الفعلية كل دورة لنبني تدريبًا يعكس ما يحدث في قاعة الاختبار فعلًا — لا تخمين ولا تنظير.'],
        ['icon' => 'landmark', 'title' => 'انضباط مؤسسي سعودي', 'desc' => 'من الرياض، بفريق تربوي وهندسي سعودي يعي نظام التعليم الثانوي وقياس بدقة، ويعمل بمعايير حوكمة صارمة لكل محتوى ينشر.'],
        ['icon' => 'sigma', 'title' => 'رياضيات أولًا… وقدرات أعمق', 'desc' => 'نمتلك محرك رياضيات متخصصًا — المعادلات والخطوات والمهارات الدقيقة — لأن إتقان الكمي يبدأ من إتقان التفصيل الرياضي.'],
    ];
    $values = [['icon' => 'crown', 'value' => 'الإتقان قبل الاستعجال'], ['icon' => 'target', 'value' => 'القياس يقود القرار'], ['icon' => 'compass', 'value' => 'كل طالب له مساره']];
@endphp
<div x-data="{ open: false }" @emperor:open-about.window="$store.windows.closeAll(); open = true" @emperor:close-windows.window="open = false">
    <x-window show="open" close="open = false" label="صفحة من نحن" heading="صفحة مستقلة — نافذة من نحن" sub="ABOUT WINDOW · MATH EMPEROR" close-label="إغلاق صفحة من نحن">
        <main class="relative mx-auto max-w-6xl px-4 pb-20 sm:px-6">
            <div class="grid-bg absolute inset-0" aria-hidden="true"></div>
            <div class="absolute -top-24 left-1/2 h-96 w-[700px] -translate-x-1/2 rounded-full bg-primary-100/70 blur-[130px] dark:bg-primary-500/8" aria-hidden="true"></div>

            {{-- hero of the window --}}
            <div class="relative pt-14 text-center sm:pt-20">
                <div data-reveal style="--ry:18px;--rd:.7s;--rdelay:.1s"><x-badge tone="primary" icon="crown">قصتنا</x-badge></div>
                <h1 data-reveal style="--ry:26px;--rd:.85s;--rdelay:.2s" class="mx-auto mt-6 max-w-3xl text-display text-foreground">
                    بنينا الإمبراطور
                    <span class="gradient-text block py-1">لأن الدرجة تستحق منهجًا</span>
                </h1>
                <p data-reveal style="--ry:20px;--rd:.8s;--rdelay:.32s" class="mx-auto mt-5 max-w-2xl text-body-lg text-foreground-2">
                    بدأنا من سؤال بسيط: لماذا يُبهر الذكاء درجة كاملة،
                    ولا نظام تحضير كامل؟ فبنينا النظام.
                </p>
            </div>

            {{-- vision card --}}
            <section data-reveal style="--ry:34px;--rd:.8s" class="relative mt-12" aria-label="رؤيتنا">
                <div class="relative overflow-hidden rounded-3xl border border-border bg-surface p-7 shadow-xl sm:p-10">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-l from-primary-400 via-primary-600 to-secondary-500" aria-hidden="true"></div>
                    <div class="grid gap-10 lg:grid-cols-5">
                        <div class="lg:col-span-3">
                            <x-badge tone="primary" size="sm" class="mb-5">رؤيتنا</x-badge>
                            <h2 class="text-title leading-snug text-foreground">
                                أن يدخل كل طالب ثانوي في المملكة اختبار قياس وهو يعرف
                                <span class="gradient-text">بالضبط</span> أين يقف، وماذا يفعل غدًا،
                                ولماذا يتدرب على هذا السؤال تحديدًا.
                            </h2>
                            <div class="section-line my-7 w-32"></div>
                            <div class="space-y-4 text-body-lg text-foreground-2">
                                <p>
                                    في ٢٠٢٤ لاحظ فريقنا أن أدوات التحضير المتاحة تعامل الطلاب كنسخة واحدة:
                                    بنك PDF متشابه، ونصائح عامة، ووقت يضيع في المكان الخطأ. أكثر من
                                    <span class="num font-black text-foreground">140,000</span> طالب
                                    وطالبة اختاروا لاحقًا طريقة مختلفة — منهج الإمبراطور.
                                </p>
                                <p>
                                    اليوم، المنصة تجمع محاكاة حقيقية بتوقيت قياس، وبنك أسئلة مصنّفًا حتى مستوى
                                    المهارة الفرعية، ومحرك خطط يعيد ترتيب أسبوعك كلما تغير أداؤك — بحيث لا توجد
                                    درجتان متماثلتان، لأنه لا يوجد طالبان متماثلان.
                                </p>
                            </div>
                            <div class="mt-7 flex flex-wrap gap-2">
                                @foreach ($values as $i => $v)
                                    <span data-reveal style="--ry:0px;--rs:.8;--rd:.5s;--rdelay:{{ 0.25 + $i * 0.1 }}s">
                                        <x-badge tone="secondary" :icon="$v['icon']">{{ $v['value'] }}</x-badge>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- side pillar stack --}}
                        <div class="grid content-start gap-4 lg:col-span-2">
                            @foreach ($pillars as $i => $p)
                                <div data-reveal style="--rx:32px;--ry:0px;--rd:.65s;--rdelay:{{ 0.1 + $i * 0.12 }}s">
                                    <x-card interactive :padded="false" class="p-5 sm:p-6">
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="grid size-11 place-items-center rounded-xl bg-primary-soft text-primary"><x-i :name="$p['icon']" class="size-5.5" /></span>
                                        </div>
                                        <h3 class="mt-4 text-subtitle text-foreground">{{ $p['title'] }}</h3>
                                        <p class="mt-1.5 text-small text-foreground-2">{{ $p['desc'] }}</p>
                                    </x-card>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- ksa band --}}
            <div data-reveal style="--ry:26px;--rd:.8s;--rdelay:.1s"
                class="mt-10 flex flex-col items-center justify-between gap-4 rounded-3xl border border-primary-100 bg-gradient-to-l from-primary-soft to-secondary-soft/60 px-7 py-6 text-center sm:flex-row sm:text-right">
                <p class="text-small font-black text-foreground">فريق تربوي وهندسي سعودي — نخدم الطالب من الرياض إلى كل منطقة في المملكة.</p>
                <span class="num rounded-full bg-surface px-4 py-2 text-[11px] font-black text-primary shadow-xs ring-1 ring-primary-100" dir="ltr">Riyadh, KSA · Since 2024</span>
            </div>

            <p class="mt-10 text-center text-caption text-foreground-3">
                اضغط <kbd class="num rounded-md border border-border bg-surface px-1.5 py-0.5 text-[10px] font-black">ESC</kbd> أو زر الإغلاق للعودة إلى الصفحة الرئيسية — موضعك محفوظ تمامًا.
            </p>
        </main>
    </x-window>
</div>
