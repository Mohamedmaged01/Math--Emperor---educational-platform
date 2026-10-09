<section id="simulator" x-data="simulator(@js(\App\Support\Content::quizQuestions()))" class="relative overflow-hidden py-20 sm:py-28">
    <div class="absolute top-0 right-1/2 h-[460px] w-[760px] translate-x-1/2 rounded-full bg-primary-100/70 blur-[140px] dark:bg-primary-500/8" aria-hidden="true"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6">
        <x-section-heading badge="تجربة تفاعلية حية" title="جرّب محاكي قياس" :inline="true" gold-title="هنا والآن"
            desc="ثلاثة أسئلة من نماذج قياس، بتوقيت رسمي وضغط حقيقي. هل أنت مستعد للتاج؟" />

        <div class="mx-auto mt-12 max-w-3xl">
            <div data-reveal style="--ry:40px;--rd:.9s" class="relative overflow-hidden rounded-3xl border border-border bg-surface shadow-xl">
                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-l from-primary-400 via-primary-600 to-secondary-500" aria-hidden="true"></div>

                {{-- ---------------- IDLE ---------------- --}}
                <div x-show="phase === 'idle'" x-transition:enter="transition duration-400" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="flex flex-col items-center px-6 py-12 text-center sm:px-12 sm:py-14">
                    <span class="anim-bob-small relative grid size-20 place-items-center rounded-3xl bg-gradient-to-br from-primary-400 via-primary-600 to-primary-800 shadow-xl shadow-primary-500/25">
                        <span class="animate-pulse-ring absolute inset-0 rounded-3xl ring-2 ring-primary-400/40" aria-hidden="true"></span>
                        <x-i name="brain-circuit" class="size-10 text-white" stroke-width="2.2" />
                    </span>

                    <h3 class="mt-6 text-title text-foreground">اختبار قصير — ٣ أسئلة</h3>
                    <p class="mt-2.5 max-w-md text-small leading-relaxed text-foreground-2">
                        عيّنة مصغّرة من المحاكي الكامل. لكل سؤال وقت محدد كما في قياس،
                        وستحصل على الشرح فور إجابتك.
                    </p>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                        @foreach (['توقيت رسمي', 'شرح فوري', 'بدون تسجيل'] as $t)
                            <x-badge tone="secondary" size="sm" icon="shield-check">{{ $t }}</x-badge>
                        @endforeach
                    </div>

                    <button type="button" @click="start()"
                        class="group mt-8 inline-flex items-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-10 py-4 text-base font-black text-white shadow-lg shadow-primary-500/25 transition-all duration-200 hover:shadow-xl hover:brightness-110 active:scale-[0.99]">
                        <x-i name="play" class="size-5 fill-current" />
                        ابدأ التحدي
                    </button>
                </div>

                {{-- ---------------- RUNNING ---------------- --}}
                <template x-if="phase === 'running'">
                    <div class="p-5 sm:p-8" x-effect="index; $el.animate([{ opacity: 0, transform: 'translateX(-30px)' }, { opacity: 1, transform: 'none' }], { duration: 450, easing: 'cubic-bezier(0.22,1,0.36,1)' })">
                        {{-- top bar --}}
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-border bg-surface-2 px-3.5 py-1.5 text-xs font-black text-foreground-2">
                                    <x-i name="crown" class="size-3.5 text-primary" />
                                    <span x-text="q.section"></span>
                                </span>
                                <span class="inline-flex items-center whitespace-nowrap rounded-full border px-3.5 py-1.5 text-xs font-black"
                                    :class="q.tag === 'كمي' ? 'bg-primary-soft text-primary border-primary-100' : 'bg-accent-soft text-accent border-accent-100'" x-text="q.tag"></span>
                            </div>

                            <div class="flex items-center gap-2">
                                <span x-show="streak >= 2" x-transition.scale.60
                                    class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border border-amber-200/60 bg-warning-soft px-2.5 py-0.5 text-[11px] font-black text-warning">
                                    <x-i name="flame" class="size-3.5" />
                                    <span class="num" x-text="'×' + streak"></span>
                                </span>
                                <div role="timer" aria-live="polite" :aria-label="'الوقت المتبقي ' + timeLeft + ' ثانية'"
                                    :class="timeLeft <= 10 && !answered ? 'border-danger/30 bg-danger-soft text-danger animate-pulse' : 'border-border bg-surface-2 text-foreground'"
                                    class="num relative inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-sm font-black transition-colors duration-300">
                                    <x-i name="alarm-clock" class="size-4" />
                                    <span x-text="clock"></span>
                                </div>
                            </div>
                        </div>

                        {{-- progress segments --}}
                        <div class="mt-5 flex items-center gap-2" aria-hidden="true">
                            <template x-for="(_, i) in questions" :key="i">
                                <span class="h-1.5 flex-1 rounded-full transition-colors duration-500"
                                    :class="i < index || (i === index && answered) ? 'bg-gradient-to-l from-primary-400 to-primary-600' : (i === index ? 'bg-primary-200 dark:bg-primary-800' : 'bg-surface-3')"></span>
                            </template>
                            <span class="mr-1 text-[11px] font-black text-foreground-3" x-text="(index + 1) + ' / ' + questions.length"></span>
                        </div>

                        {{-- timer bar --}}
                        <div class="mt-2 h-1 overflow-hidden rounded-full bg-surface-3" dir="ltr" aria-hidden="true">
                            <div class="h-full rounded-full transition-all duration-1000 ease-linear" :class="timeLeft <= 10 ? 'bg-danger' : 'bg-primary-500'" :style="'width:' + timerPct + '%'"></div>
                        </div>

                        {{-- question --}}
                        <div class="mt-6">
                            <p class="text-subtitle leading-relaxed text-foreground sm:text-lg" x-text="q.question"></p>
                            <template x-if="q.equation">
                                <div class="mt-3 my-3 flex w-full justify-start rounded-xl border border-primary-100 bg-primary-soft/60 px-4 py-3">
                                    <span class="equation text-lg text-primary-900 dark:text-primary-100" x-text="q.equation"></span>
                                </div>
                            </template>
                        </div>

                        {{-- options --}}
                        <div class="mt-5 grid gap-2.5" role="radiogroup" aria-label="خيارات الإجابة">
                            <template x-for="(opt, i) in q.options" :key="index + '-' + i">
                                <button type="button" role="radio" :aria-checked="selected === i" @click="answer(i)" :disabled="answered"
                                    class="anim-rise group flex w-full items-center justify-between rounded-xl border px-4 py-3.5 text-right text-small font-bold transition-all duration-200 active:scale-[0.985] sm:px-5"
                                    :style="'animation-delay:' + (150 + i * 80) + 'ms'" :class="optionClass(i)">
                                    <span class="flex items-center gap-3">
                                        <span class="grid size-8 shrink-0 place-items-center rounded-lg border text-xs font-black transition-colors duration-200" :class="letterClass(i)" aria-hidden="true" x-text="['أ', 'ب', 'ج', 'د'][i]"></span>
                                        <span x-text="opt"></span>
                                    </span>
                                    <span x-show="answered && i === q.answer" class="anim-pop"><x-i name="check-circle-2" class="size-5 text-success" aria-label="إجابة صحيحة" /></span>
                                    <span x-show="answered && selected === i && i !== q.answer" class="anim-pop"><x-i name="x-circle" class="size-5 text-danger" aria-label="إجابة خاطئة" /></span>
                                </button>
                            </template>
                        </div>

                        {{-- explanation + next --}}
                        <div x-show="answered" x-collapse.duration.450ms>
                            <div class="pt-[18px]">
                                <div class="rounded-2xl border border-primary-100 bg-primary-soft/60 p-4 sm:p-5" aria-live="polite">
                                    <div class="flex items-start gap-3">
                                        <span class="grid size-9 shrink-0 place-items-center rounded-xl bg-surface text-primary shadow-xs">
                                            <x-i name="lightbulb" class="size-4.5" />
                                        </span>
                                        <div>
                                            <p class="text-small font-black text-primary"
                                                x-text="selected === q.answer ? 'إجابة صحيحة — أحسنت!' : (selected === -1 ? 'انتهى الوقت — إليك الحل:' : 'إجابة غير صحيحة — إليك الحل:')"></p>
                                            <p class="mt-1 text-small leading-relaxed text-foreground-2" x-text="q.explanation"></p>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="next()"
                                    class="group mt-3.5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-6 py-3.5 text-small font-black text-white shadow-md shadow-primary-500/25 transition-all duration-200 hover:brightness-110 active:scale-[0.99]">
                                    <span x-text="index + 1 >= questions.length ? 'عرض النتيجة' : 'السؤال التالي'"></span>
                                    <x-i name="chevron-left" class="size-4.5 transition-transform duration-300 group-hover:-translate-x-1" />
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- ---------------- RESULT ---------------- --}}
                <div x-show="phase === 'result'" x-cloak
                    x-transition:enter="transition duration-500 ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="opacity-0 scale-[0.94]" x-transition:enter-end="opacity-100 scale-100"
                    class="px-6 py-10 text-center sm:px-12 sm:py-12">
                    <p class="text-caption tracking-widest text-primary">النتيجة النهائية</p>

                    <div class="relative mx-auto mt-6 grid size-44 place-items-center">
                        <svg viewBox="0 0 120 120" class="size-44 -rotate-90" aria-hidden="true">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="var(--color-surface-3)" stroke-width="8" />
                            <circle cx="60" cy="60" r="52" fill="none" stroke="var(--color-primary-500)" stroke-width="8" stroke-linecap="round" stroke-dasharray="326.7"
                                :style="'stroke-dashoffset:' + ringOffset + ';transition:stroke-dashoffset 1.4s ease-out .3s'" />
                        </svg>
                        <div class="absolute flex flex-col items-center">
                            <span class="num text-5xl font-black text-foreground"><span x-text="score"></span><span class="text-xl text-foreground-3" x-text="'/' + questions.length"></span></span>
                            <span class="mt-1 text-[11px] font-bold text-foreground-3">إجابات صحيحة</span>
                        </div>
                    </div>

                    {{-- genuine integration: the attempt is saved to the signed-in student's portal --}}
                    <div x-show="recorded" x-cloak role="status"
                        class="anim-rise mt-4 inline-flex items-center gap-2 rounded-full border border-success/30 bg-success-soft px-4 py-2 text-xs font-black text-success">
                        <x-i name="check-circle-2" class="size-4" />
                        تم تسجيل هذه النتيجة في بوابتك كطالب — افتح بوابة الطلاب لمتابعتها
                    </div>

                    <h3 class="mt-6 text-title text-foreground"
                        x-text="score === questions.length ? 'أداء إمبراطوري! التاج يليق بك' : (score >= 2 ? 'أداء قوي — خطوة من القمة' : 'بداية جيدة — التدريب يصنع الفارق')"></h3>
                    <p class="mx-auto mt-2 max-w-md text-small leading-relaxed text-foreground-2"
                        x-text="score === questions.length ? 'حللت جميع الأسئلة بثقة. تخيّل مستواك بعد ٨ أسابيع على الخطة الكاملة؟' : 'في المحاكي الكامل ستحصل على خطة تركّز على نقاط ضعفك تحديدًا حتى تتقنها.'"></p>

                    <div class="mx-auto mt-7 grid max-w-md grid-cols-3 gap-3">
                        @foreach ([['timer', "totalTime + 'ث'", 'زمن الحل'], ['zap', 'String(bestStreak)', 'أفضل سلسلة'], ['badge-check', "Math.round(score / questions.length * 100) + '٪'", 'الدقة']] as $i => [$icon, $expr, $label])
                            <div class="anim-rise rounded-2xl border border-border bg-surface-2/60 p-4" style="animation-delay: {{ 500 + $i * 100 }}ms">
                                <x-i :name="$icon" class="mx-auto size-5 text-primary" />
                                <p class="num mt-2 text-xl font-black text-foreground" x-text="{{ $expr }}"></p>
                                <p class="text-[10px] font-bold text-foreground-3">{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                        <a href="#pricing"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 px-8 py-3.5 text-small font-black text-white shadow-lg shadow-primary-500/25 transition-all duration-200 hover:brightness-110 sm:w-auto">
                            <x-i name="crown" class="size-4.5" />
                            أكمل رحلتك نحو الـ ١٠٠٪
                        </a>
                        <button type="button" @click="start()"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-border-strong bg-surface px-8 py-3.5 text-small font-black text-foreground-2 transition-colors hover:border-primary-300 hover:text-primary sm:w-auto">
                            <x-i name="rotate-ccw" class="size-4.5" />
                            إعادة المحاولة
                        </button>
                    </div>
                </div>
            </div>

            <p data-reveal="fade" style="--rd:.8s;--rdelay:.5s" class="mt-5 text-center text-caption text-foreground-3">
                هذه عيّنة من ٣ أسئلة — المحاكي الكامل يضم أقسامًا كاملة بتوقيت قياس الرسمي وتقارير أداء مفصّلة
            </p>
        </div>
    </div>
</section>
