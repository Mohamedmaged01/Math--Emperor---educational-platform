@php $chips = ['كيف أصل إلى ٩٥+؟', 'ما الفرق بين الباقات؟', 'تحدٍّ سريع: ١٢ × ٨', 'ما خطة التحضير المناسبة لي؟', 'كيف أُراجع أخطائي؟']; @endphp
<div x-data="advisor">
    {{-- launcher --}}
    <button type="button" @click="open = !open" :aria-expanded="open" aria-label="مستشار الإمبراطور الذكي — AI Qiyas Pro"
        class="anim-launcher group fixed bottom-5 left-5 z-[75] flex items-center gap-2.5 rounded-2xl bg-gradient-to-br from-primary-500 via-primary-600 to-primary-800 py-3 pe-5 ps-3.5 text-white shadow-xl shadow-primary-500/30 transition-all duration-300 hover:shadow-2xl hover:shadow-primary-500/40 hover:brightness-110 active:scale-[0.97] sm:bottom-6 sm:left-6">
        <span class="relative grid size-10 place-items-center rounded-xl bg-white/15 ring-1 ring-white/25">
            <span class="animate-pulse-ring absolute inset-0 rounded-xl ring-1 ring-white/40" aria-hidden="true"></span>
            <span x-show="open" x-cloak><x-i name="x" class="size-5" /></span>
            <span x-show="!open"><x-i name="brain-circuit" class="size-5.5" /></span>
        </span>
        <span class="text-start leading-tight">
            <span class="flex items-center gap-1.5 text-sm font-black">
                مستشار الإمبراطور
                <x-i name="sparkles" class="size-3.5 text-accent-100" />
            </span>
            <span class="num block text-[10px] font-bold text-primary-100/85">AI Qiyas Pro</span>
        </span>
    </button>

    {{-- panel --}}
    <div x-show="open" x-cloak role="dialog" aria-label="مستشار الإمبراطور الذكي"
        x-transition:enter="transition duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="opacity-0 translate-y-8 scale-[0.96]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 translate-y-6 scale-[0.97]"
        class="fixed bottom-24 left-5 z-[75] flex h-[min(600px,72vh)] w-[min(400px,calc(100vw-2.5rem))] flex-col overflow-hidden rounded-3xl border border-border bg-surface shadow-2xl sm:left-6">
        {{-- header --}}
        <div class="flex items-center gap-3 bg-gradient-to-l from-primary-600 to-primary-800 px-4 py-3.5 text-white">
            <span class="grid size-10 place-items-center rounded-xl bg-white/15 ring-1 ring-white/25">
                <x-i name="crown" class="size-5.5 text-amber-300" />
            </span>
            <div class="flex-1 leading-tight">
                <p class="text-sm font-black">مستشار الإمبراطور الذكي</p>
                <p class="text-[10px] font-bold text-primary-100/85"><span class="num">AI Qiyas Pro</span> · محلل قواعد تعليمية فوري</p>
            </div>
            <span class="flex items-center gap-1.5 rounded-full bg-white/12 px-2.5 py-1 text-[10px] font-black">
                <span class="size-1.5 rounded-full bg-emerald-300"></span>
                متصل
            </span>
        </div>

        {{-- messages --}}
        <div x-ref="list" class="flex-1 space-y-3 overflow-y-auto bg-surface-2/50 p-4">
            <template x-for="m in msgs" :key="m.id">
                <div class="flex" :class="m.role === 'user' ? 'justify-start' : 'justify-end'">
                    <div class="max-w-[85%] rounded-2xl px-4 py-3 text-small leading-relaxed shadow-xs"
                        :class="m.role === 'user' ? 'rounded-bl-md bg-primary-500 text-white' : 'rounded-br-md border border-border bg-surface text-foreground'">
                        <template x-if="m.role === 'assistant' && m.math">
                            <span class="equation mb-2 block rounded-md bg-surface-2 px-2 py-0.5 text-center text-[0.95em] text-foreground" x-text="m.math"></span>
                        </template>
                        <p :class="m.role === 'user' ? 'font-bold' : 'font-semibold'" x-text="m.text"></p>
                        <template x-if="m.action">
                            <a :href="m.action.href" @click="open = false"
                                class="mt-2.5 inline-flex items-center gap-1.5 rounded-lg bg-primary-soft px-3 py-1.5 text-[11px] font-black text-primary transition-colors hover:bg-primary-100">
                                <span x-text="m.action.label"></span>
                                <x-i name="chevron-left" class="size-3.5" />
                            </a>
                        </template>
                    </div>
                </div>
            </template>
            <div x-show="typing" class="flex justify-end">
                <div class="flex items-center gap-1.5 rounded-2xl rounded-br-md border border-border bg-surface px-4 py-3 shadow-xs" aria-label="يكتب الآن">
                    @foreach ([0, 1, 2] as $i)
                        <span class="size-1.5 animate-bounce rounded-full bg-primary-400" style="animation-delay: {{ $i * 140 }}ms"></span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- chips --}}
        <div class="flex gap-2 overflow-x-auto border-t border-border bg-surface px-3 py-2.5" dir="rtl">
            @foreach ($chips as $c)
                <button type="button" @click="send(@js($c))"
                    class="shrink-0 rounded-full border border-primary-200 bg-primary-soft/70 px-3 py-1.5 text-[11px] font-black text-primary transition-colors hover:bg-primary-100">{{ $c }}</button>
            @endforeach
        </div>

        {{-- composer --}}
        <form @submit.prevent="send(input)" class="flex items-center gap-2 border-t border-border bg-surface p-3">
            <input x-model="input" placeholder="اكتب سؤالك للمستشار…" aria-label="رسالتك إلى المستشار"
                class="h-11 flex-1 rounded-xl border border-border bg-surface-2 px-4 text-small font-bold text-foreground placeholder:text-foreground-3 focus:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
            <button type="submit" aria-label="إرسال" :disabled="!input.trim() || typing"
                class="grid size-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white shadow-md transition-all hover:brightness-110 disabled:opacity-45">
                <x-i name="send" class="size-4.5 -scale-x-100" />
            </button>
        </form>
    </div>
</div>
