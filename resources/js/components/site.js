import { api, wait } from '../lib/http';
import { createParallax } from '../lib/motion';

const uid = () => Math.random().toString(36).slice(2, 10);

/* ======================= AI Qiyas Pro — deterministic advisory engine ======================= */
const CHALLENGE = { answer: 96, options: [84, 92, 96, 108] };

function respond(raw) {
    const t = raw.trim().replace(/\s+/g, ' ');
    const num = parseInt(t.replace(/[^\d]/g, ''), 10);

    if (!Number.isNaN(num) && CHALLENGE.options.includes(num)) {
        return num === CHALLENGE.answer
            ? { id: uid(), role: 'assistant', text: 'إجابة صحيحة — ٩٦. حلّيتها بسرعة الفئة الذهبية. جرّب وضع المحاكي الكامل من قسم الاختبار في الصفحة.', math: '12 × 8 = 96 ✓', action: { label: 'جرّب المحاكي', href: '#simulator' } }
            : { id: uid(), role: 'assistant', text: `ليست ${num}. تلميح: هي أكبر قليلًا من ٩٠. أعد المحاولة، أو اطلب الحل.` };
    }
    if (/الحل|الناتج|الإجابة/.test(t) && /٩٦|96/.test(t) === false && /[×x*]/.test(t) === false) {
        return { id: uid(), role: 'assistant', text: '١٢ × ٨: قسّمها — ١٢ × ٤ = ٤٨ ثم ضاعفها = ٩٦. استراتيجية التقسيم توفّر ٤ ثوانٍ في القسم الكمي.', math: '12 × 8 = 12 × 4 × 2 = 96' };
    }

    const has = (...ks) => ks.some((k) => t.includes(k));

    if (has('باقة', 'باقات', 'سعر', 'أسعار', 'اشتراك', '٤٩٩', '١٤٩', '149', '499')) {
        return { id: uid(), role: 'assistant', text: 'ثلاث باقات بمسارات تصاعدية: المسار التأسيسي (مجانًا للأبد — اختبار تحديد مستوى + ٥٠٠ سؤال + محاكي أسبوعي) • المسار المتقدم (١٤٩ ر.س شهريًا — بنك الأسئلة كاملًا +١٢٬٥٠٠ سؤال، محاكي غير محدود، خطة ذكية، شرح لكل سؤال) • المسار الاحترافي (٤٩٩ ر.س — كل ما سبق + ٤ جلسات مباشرة + مدرب شخصي + ضمان تحسّن الدرجة).', action: { label: 'قارن الباقات تفصيليًا', href: '#pricing' } };
    }
    if (has('٩٥', '95', '١٠٠', '100', 'درجة', 'تفوق', 'أتفوق', 'كيف أرفع', 'كيف أزيد')) {
        return { id: uid(), role: 'assistant', text: 'بروتوكول ٩٥+: ١) اختبار تحديد مستوى لتحديد خط الأساس ٢) ٦–٨ أسابيع على خطة الانطلاق ٣) محاكي أسبوعي بتوقيت رسمي ٤) قاعدة ٤٠/٦٠ — ٤٠٪ تعلم و٦٠٪ تدريب ٥) مراجعة كل خطأ خلال ٢٤ ساعة ٦) آخر ٤ أسابيع: خطة الحسم بتجميعات أحدث الدورات.', action: { label: 'ابدأ الاختبار المجاني', href: '#simulator' } };
    }
    if (has('خطة', 'خطط', 'تحضير', 'مذاكرة', 'أذاكر', 'أسبوع')) {
        return { id: uid(), role: 'assistant', text: 'خطتك حسب مرحلتك: التأسيس (١٢ أسبوعًا — للصف الأول/الثاني، ٤٥ دقيقة يوميًا) • الانطلاق (٨ أسابيع — للثالث ثانوي، ٩٠ دقيقة، الأكثر اختيارًا) • الحسم (٤ أسابيع — قبل الاختبار مباشرة، ساعتان + محاكي كل ٣ أيام). الخطة تتكيف تلقائيًا مع أدائك.', action: { label: 'استعرض الخطط الذكية', href: '#plans' } };
    }
    if (has('خطأ', 'أخطاء', 'أعيد', 'مراجعة', 'أنسى')) {
        return { id: uid(), role: 'assistant', text: 'قاعدة الذهب: الخطأ الذي لا يعود إليك مرة أخرى هو خطأ مهزوم. في المنصة، كل إجابة خاطئة تُجدول تلقائيًا بصيغة مختلفة خلال ٣–٥ أيام حتى يتحول الضعف إلى إتقان. راجع أخطاء الأسبوع قبل أي محاكي جديد.', action: { label: 'شاهد كيف يعمل النظام', href: '#features' } };
    }
    if (has('تجميع', 'تجميعات', 'آخر دورة', 'دورة')) {
        return { id: uid(), role: 'assistant', text: 'تجميعات التحصيلي والقدرات تُحدّث بعد كل دورة اختبار، مصنّفة حسب نسبة التكرار. الأحدث دائمًا داخل المسار المتقدم وما فوقه.', action: { label: 'تفاصيل الباقات', href: '#pricing' } };
    }
    if (has('تحصيلي', 'تحصيليّ')) {
        return { id: uid(), role: 'assistant', text: 'التحصيلي يقيس التحصيل الدراسي (فيزياء/كيمياء/أحياء/رياضيات للمسار العلمي). ابدأ بمحاكي تشخيصي، ثم ركّز على المادة الأضعف نسبة في التقرير — أسرع مكسب درجات.' };
    }
    if (has('قدرات')) {
        return { id: uid(), role: 'assistant', text: 'القدرات كمي + لفظي بتوقيت صارم (٢٥ دقيقة/قسم تقريبًا). سرّ التفوق: متوسط أقل من ٦٠ ثانية للسؤال + عدم الرجوع وتضييع الوقت. المحاكي عندنا مطابق للتوقيت والتوزيع.' };
    }
    if (has('دخول', 'حساب', 'طالب', 'إدارة', 'معلم', 'لوحة')) {
        return { id: uid(), role: 'assistant', text: 'ثلاث مداخل: بوابة الطلاب لمتابعة درجاتك وتجميعاتك • لوحة التحكم الإمبراطورية للإدارة والمعلمين (admin / Emperor@2026 تجريبيًا). الأزرار في أعلى يمين الصفحة.', action: { label: 'إلى البوابة', href: '#top' } };
    }
    if (has('من أنت', 'من انت', 'ai', 'ذكاء', 'مستشار')) {
        return { id: uid(), role: 'assistant', text: 'أنا مستشار الإمبراطور — محرك AI Qiyas Pro: محلل قواعد تعليمية مصمم لأسئلة قياس. أجيب بسرعة فائقة عن الباقات والاستراتيجيات والحساب السريع، وأُحيلك دائمًا للمكان الصحيح في المنصة.' };
    }
    if (has('سلام', 'مرحبا', 'هلا', 'أهلا', 'هاي')) {
        return { id: uid(), role: 'assistant', text: 'هلا بك! جاهز أخدمك — اسألني عن الاستراتيجية المثلى، الباقات، أو اطلب تحديًا سريعًا تسخّن به.' };
    }
    return { id: uid(), role: 'assistant', text: 'سؤال وجيه. تخصصي الدقيق هو إرشادك في رحلة القدرات والتحصيلي: الباقات، الخطط، الاستراتيجيات، والحساب السريع. جرّب أحد الاقتراحات الجانبية أو أعد صياغة سؤالك — أو تواصل معنا مباشرة عبر قسم «اتصل بنا».' };
}

/* ======================= site search engine ======================= */
export function searchSite(entries, query) {
    const q = query.trim().toLowerCase();
    if (!q) return [];
    const scored = [];
    for (const e of entries) {
        const title = e.title.toLowerCase();
        const sub = e.sub.toLowerCase();
        let s = -1;
        if (title.startsWith(q)) s = 100;
        else if (title.includes(q)) s = 70;
        else if (sub.includes(q)) s = 40;
        if (s >= 0) scored.push({ e, s });
    }
    scored.sort((a, b) => b.s - a.s);
    return scored.slice(0, 10).map((x) => x.e);
}

const KIND_TONE = {
    'قسم': 'bg-primary-soft text-primary border-primary-100',
    'صفحة': 'bg-secondary-soft text-secondary border-secondary-100',
    'دورة': 'bg-secondary-soft text-secondary border-secondary-100',
    'أداة': 'bg-accent-soft text-accent border-accent-100',
    'خطة': 'bg-warning-soft text-warning border-amber-200/60',
    'باقة': 'bg-success-soft text-success border-success-100',
    'سؤال': 'bg-surface-2 text-foreground-2 border-border',
    'بوابة': 'bg-secondary-soft text-secondary border-secondary-100',
};

export default function registerSite(Alpine) {
    /* ---------------- hero mouse parallax ---------------- */
    Alpine.data('heroParallax', () => ({
        follow: null,
        init() {
            this.follow = createParallax(this.$el);
        },
        move(e) {
            const r = this.$el.getBoundingClientRect();
            this.follow((e.clientX - r.left) / r.width - 0.5, (e.clientY - r.top) / r.height - 0.5);
        },
    }));

    /* ---------------- stats counter (easeOutQuart, 1.8s) ---------------- */
    Alpine.data('counter', (value, decimals = 0) => ({
        display: (0).toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals }),
        run() {
            const t0 = performance.now();
            const fmt = (v) => v.toLocaleString('en-US', { minimumFractionDigits: decimals, maximumFractionDigits: decimals });
            const tick = (t) => {
                const p = Math.min((t - t0) / 1800, 1);
                this.display = fmt(value * (1 - Math.pow(1 - p, 4)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        },
    }));

    /* ---------------- Qiyas simulator ---------------- */
    Alpine.data('simulator', (questions) => ({
        questions,
        phase: 'idle',
        index: 0,
        selected: null,
        timeLeft: questions[0].time,
        score: 0,
        streak: 0,
        bestStreak: 0,
        totalTime: 0,
        answers: [],
        timer: null,
        ringOffset: 326.7,
        recorded: false,

        get q() {
            return this.questions[this.index];
        },
        get answered() {
            return this.selected !== null;
        },
        get timerPct() {
            return (this.timeLeft / this.q.time) * 100;
        },
        get clock() {
            return `${String(Math.floor(this.timeLeft / 60)).padStart(2, '0')}:${String(this.timeLeft % 60).padStart(2, '0')}`;
        },

        stopTimer() {
            clearInterval(this.timer);
            this.timer = null;
        },
        startTimer(seconds) {
            this.stopTimer();
            this.timeLeft = seconds;
            this.timer = setInterval(() => {
                if (this.timeLeft <= 1) {
                    this.timeLeft = 0;
                    this.stopTimer();
                    if (this.phase === 'running' && this.selected === null) {
                        this.selected = -1; // timeout
                        this.answers[this.index] = -1;
                        this.streak = 0;
                    }
                    return;
                }
                this.timeLeft -= 1;
            }, 1000);
        },
        start() {
            Object.assign(this, { phase: 'running', index: 0, score: 0, streak: 0, bestStreak: 0, totalTime: 0, selected: null, answers: [], recorded: false, ringOffset: 326.7 });
            this.startTimer(this.questions[0].time);
        },
        answer(i) {
            if (this.answered || this.phase !== 'running') return;
            this.stopTimer();
            this.selected = i;
            this.answers[this.index] = i;
            this.totalTime += this.q.time - this.timeLeft;
            if (i === this.q.answer) {
                this.score += 1;
                this.streak += 1;
                this.bestStreak = Math.max(this.bestStreak, this.streak);
            } else {
                this.streak = 0;
            }
        },
        next() {
            if (this.index + 1 >= this.questions.length) {
                this.phase = 'result';
                setTimeout(() => (this.ringOffset = 326.7 - (326.7 * this.score) / this.questions.length), 30);
                this.record();
                return;
            }
            this.index += 1;
            this.selected = null;
            this.startTimer(this.q.time);
        },
        /* bridge: a signed-in student's attempt is scored + stored by the server */
        async record() {
            if (window.EMPEROR?.auth?.role !== 'STUDENT') return;
            const { ok, data } = await api('/simulator/attempts', { body: { answers: this.answers } });
            this.recorded = ok && data.recorded === true;
        },
        optionClass(i) {
            const isCorrect = i === this.q.answer;
            const isChosen = this.selected === i;
            if (this.answered && isCorrect) return 'border-success/40 bg-success-soft text-success shadow-sm';
            if (this.answered && isChosen) return 'border-danger/40 bg-danger-soft text-danger';
            if (this.answered) return 'border-border bg-surface-2/60 text-foreground-3';
            return 'border-border bg-surface text-foreground hover:border-primary-300 hover:bg-primary-soft/50 hover:text-primary cursor-pointer';
        },
        letterClass(i) {
            if (this.answered && i === this.q.answer) return 'border-success/30 bg-success-soft text-success';
            if (this.answered && this.selected === i) return 'border-danger/30 bg-danger-soft text-danger';
            return 'border-border bg-surface-2 text-foreground-3 group-hover:border-primary-200 group-hover:bg-primary-soft group-hover:text-primary';
        },
        destroy() {
            this.stopTimer();
        },
    }));

    /* ---------------- smart scroll ↓ / ↑ ---------------- */
    Alpine.data('smartScroll', () => ({
        down: true,
        init() {
            let raf = null;
            const update = () => {
                raf = null;
                this.down = window.scrollY <= 340;
            };
            update();
            window.addEventListener('scroll', () => raf ?? (raf = requestAnimationFrame(update)), { passive: true });
            window.addEventListener('resize', update, { passive: true });
        },
        go() {
            const behavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';
            if (!this.down) {
                window.scrollTo({ top: 0, behavior });
                return;
            }
            const y = window.scrollY;
            const next = [...document.querySelectorAll('main section[id]')]
                .map((el) => el.getBoundingClientRect().top + y - 72)
                .filter((top) => top > y + 24)
                .sort((a, b) => a - b)[0];
            window.scrollTo({ top: next ?? document.documentElement.scrollHeight - window.innerHeight, behavior });
        },
    }));

    /* ---------------- AI advisor chat ---------------- */
    Alpine.data('advisor', () => ({
        open: false,
        input: '',
        typing: false,
        msgs: [{ id: 'welcome', role: 'assistant', text: 'أهلًا بك في مستشار الإمبراطور — Powered by AI Qiyas Pro. أجيب عن أسئلتك حول الباقات، الدرجات، الخطط، وأطرح عليك تحديات سريعة أيضًا. اختر اقتراحًا أو اكتب سؤالك.' }],
        init() {
            this.$watch('msgs', () => this.scroll());
            this.$watch('typing', () => this.scroll());
        },
        scroll() {
            this.$nextTick(() => this.$refs.list?.scrollTo({ top: this.$refs.list.scrollHeight, behavior: 'smooth' }));
        },
        send(text) {
            const clean = (text || '').trim();
            if (!clean || this.typing) return;
            this.msgs.push({ id: uid(), role: 'user', text: clean });
            this.input = '';
            this.typing = true;
            setTimeout(() => {
                this.msgs.push(respond(clean));
                this.typing = false;
            }, Math.min(500 + clean.length * 14, 1500));
        },
    }));

    /* ---------------- site search ---------------- */
    Alpine.data('siteSearch', (entries) => ({
        open: false,
        q: '',
        get hits() {
            return searchSite(entries, this.q);
        },
        show() {
            this.q = '';
            this.open = true;
            setTimeout(() => this.$refs.input?.focus(), 60);
        },
        kindClass(kind) {
            return KIND_TONE[kind] ?? KIND_TONE['سؤال'];
        },
        pick(h) {
            this.open = false;
            if (h.go.type === 'event') {
                if (h.go.target === 'emperor:open-student' || h.go.target === 'emperor:open-imperial') {
                    Alpine.store('portal').open(h.go.target.endsWith('imperial') ? 'imperial' : 'student');
                } else {
                    window.dispatchEvent(new CustomEvent(h.go.target));
                }
                return;
            }
            document.querySelector(h.go.target)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
    }));

    /* ---------------- newsletter ---------------- */
    Alpine.data('newsletter', () => ({
        email: '',
        status: 'idle',
        error: '',
        async subscribe() {
            if (this.status === 'busy') return;
            const clean = this.email.trim();
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(clean)) {
                this.error = 'اكتب بريدًا إلكترونيًا صالحًا مثل: name@example.com';
                this.status = 'error';
                return;
            }
            this.status = 'busy';
            const { ok, data } = await api('/newsletter', { body: { email: clean } });
            if (!ok) {
                this.error = data.error || 'تعذّر الاشتراك — حاول مجددًا.';
                this.status = 'error';
                return;
            }
            this.email = '';
            this.status = 'done';
            setTimeout(() => (this.status = 'idle'), 4500);
        },
    }));

    /* ---------------- contact window form ---------------- */
    Alpine.data('contactForm', () => ({
        open: false,
        busy: false,
        sent: false,
        team: 'الدعم التعليمي',
        error: null,
        form: { name: '', email: '', subject: 'استفسار عن الباقات', message: '' },
        async submit() {
            if (this.busy || this.sent) return;
            this.error = null;
            this.busy = true;
            const { ok, data } = await api('/contact', { body: this.form });
            this.busy = false;
            if (!ok) {
                this.error = data.error || 'تعذّر إرسال الرسالة — تحقق من الحقول ثم حاول مجددًا.';
                return;
            }
            this.team = data.team;
            this.sent = true;
        },
        resetForm() {
            this.sent = false;
            this.form = { name: '', email: '', subject: 'استفسار عن الباقات', message: '' };
        },
    }));

    /* ---------------- question bank window ---------------- */
    Alpine.data('questionBank', (questions) => ({
        open: false,
        level: 'الكل',
        topic: 'الكل',
        query: '',
        openId: null,
        get visible() {
            const q = this.query.trim();
            return questions.filter(
                (item) =>
                    (this.level === 'الكل' || item.level === this.level) &&
                    (this.topic === 'الكل' || item.topic === this.topic || item.skill === this.topic) &&
                    (q === '' || item.q.includes(q) || item.skill.includes(q)),
            );
        },
        levelClass(level) {
            if (level === 'سهل') return 'bg-success-soft text-success border-success-100';
            if (level === 'متوسط') return 'bg-warning-soft text-warning border-amber-200/60';
            return 'bg-danger-soft text-danger border-red-200/60';
        },
    }));

    /* ---------------- student gate + imperial console login ---------------- */
    const blankReg = () => ({ name: '', phone: '', grade: '', email: '', password: '' });

    Alpine.data('authPortal', () => ({
        modal: 'closed',
        mode: 'login',
        identifier: '',
        password: '',
        showPass: false,
        remember: false,
        busy: false,
        error: null,
        quickFilled: false,
        imperialSelection: null,
        reg: blankReg(),
        regError: null,
        resetStep: null,
        resetId: '',
        resetCode: '',
        newPass: '',
        maskedEmail: null,
        resetError: null,
        resetDone: false,

        get isImperial() {
            return this.modal === 'imperial';
        },
        get role() {
            return this.isImperial ? 'STAFF' : 'STUDENT';
        },

        openModal(m) {
            if (m !== 'closed') window.dispatchEvent(new CustomEvent('emperor:close-windows'));
            Object.assign(this, {
                modal: m, mode: 'login', identifier: '', password: '', showPass: false, remember: false, busy: false,
                error: null, quickFilled: false, imperialSelection: null, reg: blankReg(), regError: null,
                resetStep: null, resetId: '', resetCode: '', newPass: '', maskedEmail: null, resetError: null, resetDone: false,
            });
        },
        quickFill() {
            this.quickFilled = true;
            this.identifier = this.isImperial ? 'admin' : 'student@emperor.sa';
            this.password = 'Emperor@2026';
            this.remember = true;
            this.error = null;
        },
        pickImperial(kind) {
            this.imperialSelection = kind;
            this.identifier = kind === 'admin' ? 'admin' : 'fahad.zahrani@emperor.edu.sa';
            this.password = 'Emperor@2026';
            this.error = null;
            this.quickFilled = true;
        },
        async submit() {
            if (this.busy) return;
            this.error = null;
            if (!this.identifier.trim() || !this.password) {
                this.error = 'أدخل اسم المستخدم وكلمة المرور للمتابعة.';
                return;
            }
            this.busy = true;
            const { ok, data } = await api('/login', {
                body: { identifier: this.identifier, password: this.password, remember: this.remember, role: this.role },
            });
            if (ok && data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            this.busy = false;
            this.error = data.error || 'بيانات الدخول غير صحيحة. تحقق من اسم المستخدم والصلاحية.';
        },
        async submitRegister() {
            if (this.busy) return;
            this.regError = null;
            this.busy = true;
            const { ok, data } = await api('/register', { body: this.reg });
            if (ok && data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            this.busy = false;
            this.regError = { field: data.field, msg: data.error || 'تعذّر إنشاء الحساب — حاول مجددًا.' };
        },
        startReset() {
            this.resetId = this.identifier;
            this.resetStep = 'request';
            this.resetError = null;
            this.error = null;
        },
        async sendResetCode() {
            if (this.busy) return;
            if (!this.resetId.trim()) {
                this.resetError = 'اكتب اسم المستخدم أو البريد الإلكتروني المعتمد لحسابك.';
                return;
            }
            this.busy = true;
            const { ok, data } = await api('/password/request', { body: { identifier: this.resetId, role: this.role } });
            this.busy = false;
            if (!ok) {
                this.resetError = data.error;
                return;
            }
            this.resetError = null;
            this.maskedEmail = data.maskedEmail;
            this.resetStep = 'sent';
        },
        async applyNewPassword() {
            if (this.busy) return;
            this.resetError = null;
            this.busy = true;
            await wait(250);
            const { ok, data } = await api('/password/reset', {
                body: { identifier: this.resetId, role: this.role, code: this.resetCode, password: this.newPass },
            });
            this.busy = false;
            if (!ok) {
                this.resetError = data.error;
                return;
            }
            this.identifier = this.resetId;
            Object.assign(this, { password: '', newPass: '', resetCode: '', maskedEmail: null, resetStep: null, resetDone: true });
            if (this.isImperial && !this.imperialSelection) this.imperialSelection = this.identifier === 'admin' ? 'admin' : 'teacher';
            setTimeout(() => (this.resetDone = false), 7000);
        },
    }));
}
