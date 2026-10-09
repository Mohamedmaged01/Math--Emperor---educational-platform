@php
    $productLinks = [
        ['label' => 'محاكي القدرات', 'href' => '#simulator'],
        ['label' => 'محاكي التحصيلي', 'href' => '#simulator'],
        ['label' => 'بنك الأسئلة', 'href' => '#question-bank', 'event' => 'emperor:open-question-bank'],
        ['label' => 'الخطط الذكية', 'href' => '#plans'],
    ];
    $channels = array_slice(\App\Support\Content::contactChannels(), 0, 3);
    $legalCls = 'rounded-lg px-2 py-1 text-foreground-2 transition-colors hover:bg-surface-2 hover:text-primary';
@endphp
<footer class="relative border-t border-border bg-surface">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-5 lg:gap-8">
            {{-- brand --}}
            <div class="lg:col-span-2">
                <a href="#top" class="flex items-center gap-2.5" aria-label="إمبراطور الرياضيات">
                    <span class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-primary-400 via-primary-600 to-primary-800 shadow-md shadow-primary-500/25">
                        <x-i name="crown" class="size-5 text-white" stroke-width="2.4" />
                    </span>
                    <span class="leading-tight">
                        <span class="block text-base font-black text-foreground">إمبراطور الرياضيات</span>
                        <span class="num block text-[9px] font-bold tracking-[0.28em] text-primary">MATH EMPEROR</span>
                    </span>
                </a>
                <p class="mt-5 max-w-sm text-small leading-relaxed text-foreground-2">
                    منصة تعليمية سعودية متخصصة في تأهيل طلاب الثانوية لاختباري
                    القدرات العامة والتحصيلي، بمحاكاة واقعية وبنك أسئلة محدث ومحرك تعلّم
                    تكيفي — لأن إتقان الرياضيات صناعة، وليس صدفة.
                </p>
                <div class="mt-6 flex gap-2">
                    @foreach (['x' => 'إكس', 'instagram' => 'انستقرام', 'youtube' => 'يوتيوب', 'tiktok' => 'تيك توك'] as $key => $label)
                        <a href="#top" @click.prevent aria-label="{{ $label }}"
                            class="grid size-10 place-items-center rounded-xl border border-border bg-surface-2 text-foreground-3 transition-all duration-200 hover:border-primary-300 hover:bg-primary-soft hover:text-primary">
                            @switch($key)
                                @case('x')
                                    <svg viewBox="0 0 24 24" fill="currentColor" class="size-4.5" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" /></svg>
                                    @break
                                @case('instagram')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4.5" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5" ry="5" /><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" /><line x1="17.5" x2="17.51" y1="6.5" y2="6.5" /></svg>
                                    @break
                                @case('youtube')
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-4.5" aria-hidden="true"><path d="M2.5 17a24.12 24.12 0 0 1 0-10 2 2 0 0 1 1.4-1.4 49.56 49.56 0 0 1 16.2 0A2 2 0 0 1 21.5 7a24.12 24.12 0 0 1 0 10 2 2 0 0 1-1.4 1.4 49.55 49.55 0 0 1-16.2 0A2 2 0 0 1 2.5 17" /><path d="m10 15 5-3-5-3z" /></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24" fill="currentColor" class="size-4.5" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z" /></svg>
                            @endswitch
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- links --}}
            <nav aria-label="روابط المنصة">
                <h4 class="text-small font-black text-foreground">المنصة</h4>
                <ul class="mt-4 space-y-2.5">
                    @foreach ($productLinks as $l)
                        <li>
                            <a href="{{ $l['href'] }}" @if (! empty($l['event'])) @click.prevent="$dispatch('{{ $l['event'] }}')" @endif
                                class="text-small font-semibold text-foreground-2 transition-colors hover:text-primary">{{ $l['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            {{-- ===== contact + newsletter ===== --}}
            <nav aria-label="وسائل التواصل والقائمة البريدية" class="lg:col-span-2">
                <div class="flex items-end justify-between gap-3">
                    <h4 class="text-small font-black text-foreground">القائمة البريدية</h4>
                    <x-badge tone="secondary" size="sm" class="hidden sm:inline-flex">أسبوعياً للمتفوقين</x-badge>
                </div>
                <p class="mt-4 text-xs font-bold leading-relaxed text-foreground-2">
                    ملخص أسبوعي ذكي: نماذج جديدة، تجميعات، ونصائح كمي — بلا إزعاج وبريدك محفوظ.
                </p>

                <div class="mt-4 flex flex-wrap items-center gap-2.5 md:flex-nowrap">
                    <div class="flex shrink-0 items-center gap-2" role="list" aria-label="وسائل التواصل">
                        @foreach ($channels as $c)
                            <a href="{{ $c['href'] }}" @if ($c['external']) target="_blank" rel="noopener noreferrer" @endif
                                aria-label="{{ $c['label'] }}: {{ $c['value'] }}" title="{{ $c['label'] }}"
                                class="grid size-9.5 place-items-center rounded-xl border border-border bg-surface-2 text-foreground-3 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:border-primary-300 hover:bg-primary-soft hover:text-primary hover:shadow-md">
                                <x-i :name="$c['icon']" class="size-4.5" />
                            </a>
                        @endforeach
                        <a href="mailto:ahmedabdelazem495@gmail.com" aria-label="البريد الإلكتروني: ahmedabdelazem495@gmail.com" title="البريد الإلكتروني"
                            class="grid size-9.5 place-items-center rounded-xl border border-border bg-surface-2 text-accent shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:border-accent-500/40 hover:bg-accent-soft hover:shadow-md">
                            <x-i name="mail" class="size-4.5" />
                        </a>
                    </div>

                    {{-- newsletter (stored in the database) --}}
                    <div x-data="newsletter" class="flex min-w-0 flex-1">
                        <div x-show="status === 'done'" x-cloak role="status" class="flex h-11 flex-1 items-center gap-2 rounded-xl border border-success/30 bg-success-soft px-3.5">
                            <x-i name="check-circle-2" class="size-4.5 shrink-0 text-success" />
                            <p class="text-xs font-black text-success">تم الاشتراك بنجاح</p>
                        </div>
                        <form x-show="status !== 'done'" @submit.prevent="subscribe()" novalidate class="flex min-w-0 flex-1 items-center gap-2">
                            <div class="flex flex-1 overflow-hidden rounded-xl border border-border bg-surface-2 shadow-xs focus-within:border-primary-400 focus-within:ring-2 focus-within:ring-primary-500/20">
                                <label for="newsletter-email" class="sr-only">البريد الإلكتروني للقائمة البريدية</label>
                                <input id="newsletter-email" type="email" x-model="email" @input="status === 'error' && (status = 'idle')"
                                    placeholder="بريدك الإلكتروني…" dir="ltr"
                                    class="h-11 min-w-0 flex-1 bg-transparent px-3.5 text-small font-bold text-foreground placeholder:text-foreground-3 focus:outline-none" />
                                <button type="submit" :disabled="status === 'busy'" aria-label="اشترك في القائمة البريدية"
                                    class="grid w-12 shrink-0 place-items-center bg-gradient-to-b from-primary-500 to-primary-700 text-white transition-all hover:brightness-110 disabled:opacity-60">
                                    <span x-show="status === 'busy'" x-cloak><x-i name="loader-2" class="size-4 animate-spin" /></span>
                                    <span x-show="status !== 'busy'"><x-i name="send" class="size-4.5 -scale-x-100" /></span>
                                </button>
                            </div>
                            <p x-show="status === 'error'" x-cloak role="alert" class="sr-only" x-text="error"></p>
                            <span x-show="status === 'error'" x-cloak :title="error" :aria-label="error"
                                class="grid size-9 shrink-0 place-items-center rounded-xl border border-danger/30 bg-danger-soft text-danger">
                                <x-i name="shield-check" class="size-4" />
                            </span>
                        </form>
                    </div>
                </div>

                <p class="mt-3 flex items-center gap-1.5 text-[10px] font-bold text-foreground-3">
                    <x-i name="shield-check" class="size-3.5 text-secondary" />
                    لا نشارك بريدك — إلغاء الاشتراك بضغطة زر في أي وقت.
                </p>

                <div class="mx-auto mt-4 flex w-fit items-center gap-2.5 md:mx-0" role="note" aria-label="رقم الهاتف">
                    <a href="tel:+966500523516" aria-label="اتصل على الرقم +966 50 052 3516" title="اتصل الآن"
                        class="grid size-9.5 shrink-0 place-items-center rounded-xl border border-success/40 bg-success-soft text-success shadow-xs transition-all hover:-translate-y-0.5 hover:shadow-md">
                        <x-i name="phone" class="size-4.5" />
                    </a>
                    <a href="tel:+966500523516" dir="ltr" aria-label="رقم الهاتف +966 50 052 3516"
                        class="num text-base font-black tracking-wide text-success transition-colors hover:text-success-600 hover:underline">+966 50 052 3516</a>
                </div>
            </nav>
        </div>

        {{-- legal strip --}}
        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-border pt-5 lg:flex-row">
            <nav aria-label="روابط السياسات والبوابات" class="flex flex-wrap items-center justify-center gap-x-2.5 gap-y-2 text-[11px] font-black">
                <button type="button" @click="$dispatch('emperor:open-legal', 'privacy')" class="{{ $legalCls }}">سياسة الخصوصية</button>
                <span class="text-foreground-3/30" aria-hidden="true">•</span>
                <button type="button" @click="$dispatch('emperor:open-legal', 'terms')" class="{{ $legalCls }}">الشروط والأحكام</button>
                <span class="text-foreground-3/30" aria-hidden="true">•</span>
                <button type="button" @click="$dispatch('emperor:open-legal', 'policy')" class="{{ $legalCls }}">سياسة المنصة</button>
                <span class="text-foreground-3/30" aria-hidden="true">•</span>
                <button type="button" @click="$dispatch('emperor:open-contact')" class="{{ $legalCls }}">اتصل بنا</button>
            </nav>

            <p class="text-center text-[11px] font-semibold leading-relaxed text-foreground-3 lg:text-left">
                جميع الحقوق محفوظة © ٢٠٢٦ • منصة الإمبراطور التعليمية
                <span class="mx-2 opacity-50" aria-hidden="true">—</span>
                تطوير
                <a href="https://skadtech.com" target="_blank" rel="noopener noreferrer" class="font-black text-primary transition-colors hover:text-primary-hover hover:underline">سكاد تيك</a>
            </p>
        </div>
    </div>
</footer>
