@php
    $isSuper = $role === 'SUPER_ADMIN';
    $kind = $mod['kind'];
@endphp
<x-layouts.app :title="$mod['label'].' — لوحة التحكم الإمبراطورية'" robots="noindex, nofollow">
    <div x-data="imperialShell({ module: @js($mod['id']), palette: @js($palette) })" class="flex h-dvh bg-background" role="application" aria-label="لوحة التحكم الإمبراطورية">

        {{-- ============================ SIDEBAR ============================ --}}
        <div x-show="mobileNav" x-cloak x-transition.opacity @click="mobileNav = false" class="fixed inset-0 z-10 bg-foreground/40 lg:hidden"></div>
        <aside x-show="sidebarOpen || mobileNav" x-cloak
            x-transition:enter="transition duration-[350ms] ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="translate-x-80" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-80"
            :class="mobileNav ? 'fixed inset-y-0 right-0 z-20 flex shadow-2xl lg:hidden' : 'hidden lg:flex'"
            class="z-20 h-full w-72 shrink-0 flex-col border-s border-border bg-surface">
            {{-- identity --}}
            <div class="flex items-center gap-3 border-b border-border px-5 py-4">
                <span class="grid size-10 place-items-center rounded-xl bg-gradient-to-br from-primary-400 via-primary-600 to-primary-800 shadow-md shadow-primary-500/25">
                    <x-i name="crown" class="size-5 text-white" stroke-width="2.2" />
                </span>
                <div class="min-w-0 flex-1 leading-tight">
                    <p class="truncate text-sm font-black text-foreground">لوحة التحكم الإمبراطورية</p>
                    <p class="num text-[9px] font-bold tracking-wider text-foreground-3">IMPERIAL CONTROL CENTER</p>
                </div>
                <button type="button" @click="sidebarOpen = false; mobileNav = false" class="text-foreground-3 hover:text-foreground" aria-label="طي الشريط">
                    <x-i name="panel-right-open" class="size-4.5" />
                </button>
            </div>

            {{-- role card --}}
            <div class="mx-3 mt-3 rounded-2xl border border-primary-100 bg-primary-soft/60 p-3.5">
                <div class="flex items-center gap-2.5">
                    <span class="grid size-10 place-items-center rounded-xl bg-surface text-primary shadow-xs"><x-i name="shield-check" class="size-5" /></span>
                    <div class="leading-tight">
                        <p class="text-xs font-black text-foreground">{{ $isSuper ? 'الإدارة العليا' : 'المعلم المعتمد' }}</p>
                        <p class="text-[10px] font-bold text-primary">{{ $isSuper ? 'كامل الصلاحيات' : 'صلاحيات تدريسية محدودة' }}</p>
                    </div>
                    <span class="mr-auto size-2 rounded-full bg-success" aria-hidden="true"></span>
                </div>
            </div>

            {{-- dynamic nav (filtered by RBAC) --}}
            <nav class="flex-1 overflow-y-auto px-3 py-3" aria-label="وحدات الإدارة">
                @foreach ($groups as $group => $items)
                    <div class="mb-4">
                        <p class="mb-1.5 px-2 text-[10px] font-black tracking-wider text-foreground-3">{{ $group }}</p>
                        <ul class="space-y-0.5">
                            @foreach ($items as $m)
                                @php $active = $mod['id'] === $m['id']; @endphp
                                <li>
                                    <a href="{{ route('imperial.show', $m['id']) }}"
                                        @class([
                                            'group flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-start text-small font-black transition-all duration-200',
                                            'bg-gradient-to-l from-primary-500 to-primary-700 text-white shadow-md shadow-primary-500/25' => $active,
                                            'text-foreground-2 hover:bg-surface-2 hover:text-primary' => ! $active,
                                        ])>
                                        <x-i :name="$m['icon']" class="size-4.5 {{ $active ? 'text-white' : 'text-foreground-3 group-hover:text-primary' }}" />
                                        <span class="flex-1 truncate">{{ $m['label'] }}</span>
                                        @if ($m['id'] === 'approvals' && $pendingApprovals > 0)
                                            <span @class(['num rounded-full px-1.5 py-0.5 text-[9px] font-black', 'bg-white/20 text-white' => $active, 'bg-danger-soft text-danger' => ! $active])>{{ $pendingApprovals }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>

            {{-- reset workspace --}}
            <div class="border-t border-border p-3">
                <button type="button" @click="resetAsk = true"
                    class="flex w-full items-center justify-center gap-2 rounded-xl border border-border-strong px-3 py-2.5 text-[11px] font-black text-foreground-3 transition-colors hover:border-danger/40 hover:text-danger">
                    <x-i name="refresh-ccw" class="size-3.5" />
                    إعادة تعيين مساحة العمل التجريبية
                </button>
            </div>
        </aside>

        {{-- ============================ MAIN ============================ --}}
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex h-16 shrink-0 items-center gap-2 border-b border-border bg-surface px-3.5 sm:px-5">
                <button type="button" @click="sidebarOpen = !sidebarOpen" class="hidden size-10 place-items-center rounded-xl border border-border text-foreground-2 lg:grid" aria-label="تبديل الشريط">
                    <span x-show="sidebarOpen"><x-i name="panel-right-close" class="size-5" /></span>
                    <span x-show="!sidebarOpen" x-cloak><x-i name="panel-right-open" class="size-5" /></span>
                </button>
                <button type="button" @click="mobileNav = true" class="grid size-10 place-items-center rounded-xl border border-border text-foreground-2 lg:hidden" aria-label="القائمة">
                    <x-i name="menu" class="size-5" />
                </button>

                <div class="min-w-0">
                    <h1 class="truncate text-sm font-black text-foreground sm:text-base">{{ $mod['label'] }}</h1>
                    <p class="truncate text-[10px] font-bold text-foreground-3">{{ $mod['summary'] }}</p>
                </div>

                <button type="button" @click="paletteOpen = true"
                    class="mx-1 hidden h-10 min-w-48 flex-1 items-center gap-2 rounded-xl border border-border bg-surface-2/70 px-3.5 text-small font-bold text-foreground-3 transition-colors hover:border-primary-300 sm:flex">
                    <x-i name="search" class="size-4" />
                    بحث شامل أو إجراء…
                    <kbd class="num mr-auto inline-flex items-center gap-0.5 rounded-md border border-border bg-surface px-1.5 py-0.5 text-[9px] font-black text-foreground-3"><x-i name="command" class="size-2.5" />K</kbd>
                </button>

                <div class="mr-auto flex items-center gap-2">
                    <button type="button" @click="notifs = true" aria-label="الإشعارات"
                        class="relative grid size-10 place-items-center rounded-xl border border-border bg-surface text-foreground-2 shadow-xs transition-colors hover:text-primary">
                        <x-i name="bell-ring" class="size-4.5" />
                        <span class="absolute -top-1 -end-1 grid size-4.5 place-items-center rounded-full bg-danger text-[9px] font-black text-white">3</span>
                    </button>
                    <x-theme-toggle />
                    <a href="{{ url('/') }}" aria-label="الذهاب إلى الصفحة الرئيسية" title="الصفحة الرئيسية — جلستك تبقى نشطة"
                        class="hidden h-10 items-center gap-1.5 rounded-xl border border-border px-3.5 text-xs font-black text-foreground-2 transition-colors hover:border-primary-300 hover:text-primary md:inline-flex">
                        <x-i name="house" class="size-4" /> الصفحة الرئيسية
                    </a>
                    <div class="hidden items-center gap-2 rounded-xl border border-border px-3 py-1.5 md:flex">
                        <span class="grid size-8 place-items-center rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-xs font-black text-white">{{ mb_substr($user->name, 0, 1) }}</span>
                        <div class="leading-tight">
                            <p class="max-w-28 truncate text-[11px] font-black text-foreground">{{ $user->name }}</p>
                            <p class="num text-[9px] font-bold text-success">جلسة نشطة · {{ $isSuper ? 'admin' : 'teacher' }}</p>
                        </div>
                        <x-i name="chevrons-up-down" class="size-3.5 text-foreground-3" />
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" aria-label="تسجيل الخروج" class="grid size-10 place-items-center rounded-xl border border-danger/25 bg-danger-soft text-danger">
                            <x-i name="log-out" class="size-4.5" />
                        </button>
                    </form>
                </div>
            </header>

            {{-- workspace --}}
            <main class="min-w-0 flex-1 overflow-y-auto p-4 sm:p-6" style="scrollbar-gutter: stable">
                @if ($denied)
                    <x-card class="mx-auto mt-16 max-w-md py-12 text-center">
                        <x-i name="lock-keyhole" class="mx-auto size-12 text-danger" />
                        <h2 class="mt-4 text-title">غير مصرح بالوصول</h2>
                        <p class="mt-2 text-small text-foreground-2">صلاحيات حسابك لا تخوّل عرض هذه الوحدة. تم تسجيل المحاولة بأمان.</p>
                        <x-button class="mt-6" :href="route('imperial.show')">العودة للوحة القيادة</x-button>
                    </x-card>
                @else
                    @include('imperial.modules.'.$kind)
                @endif
            </main>
        </div>

        {{-- notifications drawer --}}
        <div x-show="notifs" x-cloak x-transition.opacity @click="notifs = false" class="fixed inset-0 z-40 bg-foreground/30"></div>
        <aside x-show="notifs" x-cloak
            x-transition:enter="transition duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="-translate-x-[340px]" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-[340px]"
            class="fixed inset-y-0 left-0 z-50 flex w-80 flex-col border-e border-border bg-surface shadow-2xl">
            <div class="flex items-center justify-between border-b border-border px-4 py-3.5">
                <h3 class="text-subtitle">التنبيهات التشغيلية</h3>
                <button type="button" @click="notifs = false" aria-label="إغلاق" class="text-foreground-3 hover:text-foreground"><x-i name="x" class="size-4.5" /></button>
            </div>
            <div class="flex-1 overflow-y-auto p-3">
                @foreach ([['٣ موافقات بانتظار قرارك', 'warning', 'approvals'], ['طلب مؤسسي — مدرسة الرواد بانتظار الفوترة النهائية', 'primary', 'commerce'], ['٢٨ سؤالًا في طابور المراجعة الأسبوعي', 'accent', 'questions']] as [$t, $tone, $go])
                    <button type="button" @click="go(@js($go), {{ $canView[$go] ? 'true' : 'false' }})"
                        class="mb-2 flex w-full items-center gap-3 rounded-xl border border-border bg-surface-2/60 p-3 text-start transition-colors hover:border-primary-300">
                        <x-badge :tone="$tone" size="sm"><x-i name="sparkles" class="size-3" /></x-badge>
                        <span class="text-xs font-bold text-foreground">{{ $t }}</span>
                    </button>
                @endforeach
            </div>
        </aside>

        {{-- command palette (Ctrl+K) --}}
        <template x-teleport="body">
            <div x-show="paletteOpen" x-cloak>
                <div x-show="paletteOpen" x-transition.opacity @click="paletteOpen = false" class="fixed inset-0 z-[80] bg-foreground/45 backdrop-blur-sm"></div>
                <div x-show="paletteOpen" role="dialog" aria-label="مركز الأوامر"
                    x-transition:enter="transition duration-250 ease-[cubic-bezier(0.22,1,0.36,1)]" x-transition:enter-start="opacity-0 -translate-y-4 scale-[0.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    class="fixed inset-x-0 top-[12%] z-[85] mx-auto w-[min(620px,92vw)] overflow-hidden rounded-2xl border border-border bg-surface shadow-2xl">
                    <div class="flex items-center gap-2 border-b border-border px-4 py-3.5">
                        <x-i name="search" class="size-4.5 text-primary" />
                        <input x-ref="paletteInput" x-model="paletteQ" placeholder="ابحث بصلاحياتك: وحدة، طالب، معلم، سؤال، نموذج، إجراء…" aria-label="بحث مركز الأوامر"
                            class="h-8 flex-1 bg-transparent text-small font-bold text-foreground placeholder:text-foreground-3 focus:outline-none" />
                        <kbd class="num rounded-md border border-border bg-surface-2 px-2 py-1 text-[9px] font-black text-foreground-3">ESC</kbd>
                    </div>
                    <div class="max-h-[46vh] overflow-y-auto p-2">
                        <p x-show="paletteHits.length === 0" class="px-4 py-8 text-center text-small font-bold text-foreground-3">لا نتائج ضمن صلاحياتك وبحثك الحالي</p>
                        <template x-for="(h, i) in paletteHits" :key="h.id">
                            <button type="button" @click="go(h.target, h.allowed)" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-start transition-colors hover:bg-surface-2">
                                <span class="num grid size-8 shrink-0 place-items-center rounded-lg bg-primary-soft text-[10px] font-black text-primary" dir="ltr" x-text="h.kind === 'وحدة' ? '◈' : i + 1"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-small font-black text-foreground" x-text="h.label"></p>
                                    <p class="truncate text-[10px] font-bold text-foreground-3" x-text="h.sub"></p>
                                </div>
                                <span class="inline-flex items-center whitespace-nowrap rounded-full border border-border bg-surface-2 px-2.5 py-0.5 text-[11px] font-black text-foreground-2" x-text="h.kind"></span>
                            </button>
                        </template>
                        <p class="px-4 py-2 text-center text-[10px] font-bold text-foreground-3">تُعرض النتائج من وحدات تملك صلاحية الوصول إليها فقط — النتائج المحظورة لا تظهر أصلًا</p>
                    </div>
                </div>
            </div>
        </template>

        {{-- reset confirm --}}
        <x-modal show="resetAsk" close="resetAsk = false" title="إعادة تعيين مساحة العمل" size="sm">
            <p class="text-small font-bold leading-relaxed text-foreground-2">
                ستُعاد كل السجلات والإعدادات وسجل التدقيق إلى بيانات البذور الأولية. هذا يسهّل العرض المنتظم للمساحة التجريبية — سيُوثق الإجراء.
            </p>
            <p x-show="resetError" x-cloak class="mt-3 rounded-xl border border-danger/25 bg-danger-soft px-3.5 py-2.5 text-xs font-bold text-danger" x-text="resetError"></p>
            <div class="mt-5 flex gap-2">
                <x-button variant="danger" full-width @click="resetWorkspace()" ::disabled="busy">إعادة تعيين الآن</x-button>
                <x-button variant="ghost" @click="resetAsk = false">إلغاء</x-button>
            </div>
        </x-modal>

        {{-- toast (server refusals / errors) --}}
        <div x-show="toast" x-cloak x-transition role="alert"
            class="fixed bottom-5 left-1/2 z-[95] flex -translate-x-1/2 items-center gap-2 rounded-xl border border-danger/25 bg-danger-soft px-4 py-3 text-xs font-black text-danger shadow-lg">
            <x-i name="shield-alert" class="size-4" /><span x-text="toast"></span>
        </div>
    </div>
</x-layouts.app>
