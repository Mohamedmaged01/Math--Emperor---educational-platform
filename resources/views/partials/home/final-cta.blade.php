<section class="relative px-4 pb-20 sm:px-6 sm:pb-28">
    <div data-reveal style="--ry:50px;--rs:.98;--rd:1s"
        class="relative mx-auto max-w-6xl overflow-hidden rounded-[2rem] border border-primary-800/30 shadow-2xl shadow-primary-900/20">
        {{-- royal blue gradient panel — intentional dark moment inside light page --}}
        <div class="absolute inset-0 bg-gradient-to-bl from-primary-900 via-primary-800 to-[#122458]" aria-hidden="true"></div>
        <div class="absolute -top-32 right-1/2 h-72 w-[36rem] translate-x-1/2 rounded-full bg-secondary-500/20 blur-[110px]" aria-hidden="true"></div>
        <div class="absolute inset-0 opacity-[0.14]" aria-hidden="true"
            style="background-image: linear-gradient(rgba(255,255,255,0.25) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.25) 1px, transparent 1px); background-size: 48px 48px; mask-image: radial-gradient(ellipse 80% 70% at 50% 0%, black, transparent 72%); -webkit-mask-image: radial-gradient(ellipse 80% 70% at 50% 0%, black, transparent 72%);"></div>

        <div class="anim-sigma absolute -right-10 -bottom-14 opacity-[0.08]" aria-hidden="true">
            <x-i name="sigma" class="size-72 text-white" stroke-width="1" />
        </div>

        <div class="relative px-6 py-14 text-center sm:px-16 sm:py-18">
            <span data-reveal style="--ry:0px;--rs:.8;--rd:.7s;--rdelay:.2s" class="mx-auto grid size-18 place-items-center rounded-3xl bg-white/10 ring-1 ring-white/25 backdrop-blur">
                <x-i name="crown" class="size-9 text-amber-300" stroke-width="2" />
            </span>

            <h2 data-reveal style="--ry:26px;--rd:.8s;--rdelay:.3s" class="mx-auto mt-7 max-w-2xl text-headline text-white">
                مقعدك في الصف الأول
                <span class="bg-gradient-to-l from-secondary-100 via-white to-primary-200 bg-clip-text py-1 text-transparent">بانتظارك</span>
            </h2>

            <p data-reveal style="--ry:20px;--rd:.8s;--rdelay:.4s" class="mx-auto mt-4 max-w-xl text-body-lg text-primary-100/90">
                انضم إلى أكثر من ١٤٢ ألف طالب قرروا أن تكون الرياضيات قصة تفوّق —
                لا مصدر قلق. ابدأ مجانًا اليوم.
            </p>

            <div data-reveal style="--ry:20px;--rd:.8s;--rdelay:.5s" class="mt-8 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="#pricing"
                    class="group inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-10 py-4 text-base font-black text-primary-800 shadow-xl transition-all duration-200 hover:brightness-105 hover:shadow-2xl active:scale-[0.99] sm:w-auto">
                    <x-i name="sparkles" class="size-5 text-primary-600" />
                    أنشئ حسابك المجاني
                    <x-i name="chevron-left" class="size-5 transition-transform duration-300 group-hover:-translate-x-1" />
                </a>
                <span class="inline-flex items-center gap-2 text-xs font-bold text-primary-100/80">
                    <x-i name="shield-check" class="size-4 text-secondary-100" />
                    بدون بطاقة ائتمانية · تفعيل فوري
                </span>
            </div>
        </div>
    </div>
</section>
