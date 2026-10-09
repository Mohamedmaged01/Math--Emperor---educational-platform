@php
    use App\Support\Content;
    $channels = Content::contactChannels();
    $subjects = Content::contactSubjects();
    $inputCls = 'h-12 w-full rounded-xl border border-border bg-surface-2 px-4 text-small font-bold text-foreground placeholder:text-foreground-3 transition-all focus:border-primary-400 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary-500/25';
@endphp
<div x-data="contactForm" @emperor:open-contact.window="$store.windows.closeAll(); open = true" @emperor:close-windows.window="open = false">
    <x-window show="open" close="open = false" label="صفحة اتصل بنا" heading="صفحة مستقلة — التواصل" sub="CONTACT WINDOW · MATH EMPEROR" sub-tone="text-secondary" close-label="إغلاق صفحة التواصل">
        <main class="relative mx-auto max-w-6xl px-4 pb-20 sm:px-6">
            <div class="absolute -top-24 left-1/2 h-96 w-[700px] -translate-x-1/2 rounded-full bg-secondary-100/70 blur-[130px] dark:bg-secondary-500/8" aria-hidden="true"></div>
            <div class="absolute bottom-10 -left-40 h-96 w-96 rounded-full bg-primary-100/60 blur-[130px] dark:bg-primary-500/8" aria-hidden="true"></div>

            <div class="relative pt-14 text-center sm:pt-16">
                <div data-reveal style="--ry:18px;--rd:.7s;--rdelay:.1s"><x-badge tone="secondary" icon="message-circle">اتصل بنا • خدمة ودعم الطلاب</x-badge></div>
                <h1 data-reveal style="--ry:26px;--rd:.85s;--rdelay:.2s" class="mx-auto mt-6 max-w-2xl text-display text-foreground">
                    فريق الدعم الفني والأكاديمي
                    <span class="gradient-text block py-1">متواجد لخدمتك على مدار الساعة</span>
                </h1>
                <p data-reveal style="--ry:20px;--rd:.8s;--rdelay:.32s" class="mx-auto mt-5 max-w-xl text-body-lg text-foreground-2">
                    محادثة واتساب فورية، اتصال مباشر، قناة تيليجرام، أو بريد إلكتروني —
                    اختر قناتك المفضلة وسنكون معك مباشرة.
                </p>
            </div>

            <div class="relative mt-12 grid gap-6 lg:grid-cols-5">
                {{-- channels --}}
                <div class="space-y-4 lg:col-span-2">
                    @foreach ($channels as $i => $c)
                        <a href="{{ $c['href'] }}" @if ($c['external']) target="_blank" rel="noopener noreferrer" @endif aria-label="{{ $c['title'] }}: {{ $c['value'] }}"
                            data-reveal style="--ry:18px;--rd:.55s;--rdelay:{{ 0.1 + $i * 0.1 }}s" class="group block">
                            <x-card interactive class="flex items-center gap-4 group-hover:border-primary-300">
                                <span class="grid size-13 shrink-0 place-items-center rounded-2xl transition-transform duration-300 group-hover:scale-110 {{ $c['accent'] }}"><x-i :name="$c['icon']" class="size-6" /></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-caption text-foreground-3">{{ $c['title'] }}</p>
                                    <p class="num truncate text-left text-small font-black text-foreground" dir="ltr">{{ $c['value'] }}</p>
                                    <p class="text-[10px] font-bold text-foreground-3">{{ $c['note'] }}</p>
                                </div>
                                <span class="grid size-8 place-items-center rounded-full bg-surface-2 text-foreground-3 transition-all duration-300 group-hover:bg-primary-soft group-hover:text-primary" aria-hidden="true">
                                    <x-i name="chevron-left" class="size-4 transition-transform duration-300 group-hover:-translate-x-1" />
                                </span>
                            </x-card>
                        </a>
                    @endforeach

                    <div data-reveal style="--ry:18px;--rd:.55s;--rdelay:.4s">
                        <x-card class="border-secondary-100 bg-secondary-soft/60">
                            <div class="flex items-start gap-3">
                                <x-i name="shield-check" class="mt-0.5 size-5 shrink-0 text-secondary" />
                                <div>
                                    <p class="text-small font-black text-foreground">خصوصيتك محفوظة</p>
                                    <p class="mt-1 text-xs font-bold leading-relaxed text-foreground-3">لا نشارك بياناتك مع أي طرف ثالث، ولا نرسل رسائل تسويقية إلا بموافقتك الصريحة.</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center gap-2 text-[10px] font-black text-foreground-3">
                                <x-i name="clock-3" class="size-3.5" />
                                أسرع رد: واتساب الفوري على مدار الساعة — <span class="num" dir="ltr">+966 50 052 3516</span>
                            </div>
                        </x-card>
                    </div>
                </div>

                {{-- form --}}
                <div data-reveal style="--ry:32px;--rd:.8s;--rdelay:.15s" class="lg:col-span-3">
                    <x-card class="p-6 sm:p-8">
                        <div x-show="sent" x-cloak class="flex flex-col items-center py-14 text-center"
                            x-transition:enter="transition duration-[450ms] ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="opacity-0 scale-[0.94]" x-transition:enter-end="opacity-100 scale-100">
                            <span class="anim-pop grid size-20 place-items-center rounded-full bg-success-soft text-success" style="animation-delay:.15s"><x-i name="check-circle-2" class="size-10" /></span>
                            <h3 class="mt-5 text-title text-foreground">وصلت رسالتك بنجاح</h3>
                            <p class="mt-2 max-w-sm text-small leading-relaxed text-foreground-3">
                                أُسند طلبك إلى فريق <span x-text="team"></span> — ستصلك إجابة على بريدك خلال ساعات العمل.
                            </p>
                            <button type="button" @click="resetForm()"
                                class="mt-6 rounded-xl border border-border-strong bg-surface px-6 py-3 text-small font-black text-foreground-2 transition-colors hover:border-primary-300 hover:text-primary">إرسال رسالة أخرى</button>
                        </div>

                        <form x-show="!sent" @submit.prevent="submit()" class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="block">
                                    <span class="mb-1.5 block text-xs font-black text-foreground-2">الاسم الكريم *</span>
                                    <input required x-model="form.name" placeholder="اسمك الكامل" class="{{ $inputCls }}" />
                                </label>
                                <label class="block">
                                    <span class="mb-1.5 block text-xs font-black text-foreground-2">البريد الإلكتروني *</span>
                                    <input required type="email" x-model="form.email" placeholder="you@example.com" dir="ltr" class="{{ $inputCls }} text-left" />
                                </label>
                            </div>
                            <label class="block">
                                <span class="mb-1.5 block text-xs font-black text-foreground-2">موضوع الرسالة</span>
                                <select x-model="form.subject" class="{{ $inputCls }} appearance-none">
                                    @foreach ($subjects as $s)
                                        <option>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block">
                                <span class="mb-1.5 block text-xs font-black text-foreground-2">رسالتك *</span>
                                <textarea required rows="5" x-model="form.message" placeholder="اكتب سؤالك بالتفصيل — كلما زادت المعلومات، كانت إجابتنا أدق…"
                                    class="w-full rounded-xl border border-border bg-surface-2 px-4 py-3 text-small font-bold leading-relaxed text-foreground placeholder:text-foreground-3 transition-all focus:border-primary-400 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-primary-500/25"></textarea>
                            </label>
                            <p x-show="error" x-cloak role="alert" class="flex items-start gap-2 rounded-xl border border-danger/25 bg-danger-soft px-3.5 py-2.5 text-xs font-bold text-danger">
                                <x-i name="shield-check" class="mt-0.5 size-4 shrink-0" /><span x-text="error"></span>
                            </p>
                            <button type="submit" :disabled="busy"
                                class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-l from-primary-500 to-primary-700 text-small font-black text-white shadow-lg shadow-primary-500/25 transition-all duration-200 hover:brightness-110 active:scale-[0.99] disabled:opacity-60">
                                <span x-show="busy" class="inline-flex items-center gap-2"><x-i name="loader-2" class="size-4.5 animate-spin" />جارٍ الإرسال…</span>
                                <span x-show="!busy" class="inline-flex items-center gap-2"><x-i name="send" class="size-4.5 -scale-x-100" />إرسال الرسالة</span>
                            </button>
                        </form>
                    </x-card>
                </div>
            </div>

            <p class="mt-10 text-center text-caption text-foreground-3">
                اضغط <kbd class="num rounded-md border border-border bg-surface px-1.5 py-0.5 text-[10px] font-black">ESC</kbd> أو زر الإغلاق للعودة إلى الصفحة الرئيسية.
            </p>
        </main>
    </x-window>
</div>
