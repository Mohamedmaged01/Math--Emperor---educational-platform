import { api } from '../lib/http';

const API = '/imperial/api';

/* status → badge tone (mirror of the DataTable tone map) */
const TONE_CLASS = {
    success: 'bg-success-soft text-success border-success-100',
    warning: 'bg-warning-soft text-warning border-amber-200/60',
    danger: 'bg-danger-soft text-danger border-red-200/60',
    primary: 'bg-primary-soft text-primary border-primary-100',
    neutral: 'bg-surface-2 text-foreground-2 border-border',
    accent: 'bg-accent-soft text-accent border-accent-100',
    secondary: 'bg-secondary-soft text-secondary border-secondary-100',
};
const STATUS_TONES = {
    'نشط': 'success', 'معتمد': 'success', 'منشور': 'success', 'مكتمل': 'success', 'مدفوع': 'success', 'مفعّل': 'success', 'true': 'success', 'success': 'success',
    'قيد المراجعة': 'warning', 'مجدول': 'warning', 'قيد الإرسال': 'warning', 'معتمد — بانتظار النشر': 'warning', 'قيد البناء': 'warning', 'مراقبة': 'warning', 'معلق': 'warning',
    'مسودة': 'neutral', 'مؤرشف': 'neutral', 'طلب اعتماد — قيد المراجعة': 'primary', 'موقوف — يتجاوز المدة': 'warning',
    'فشل — بطاقة مرفوضة': 'danger', 'فاشل': 'danger', 'حرج': 'danger', 'موقوف': 'danger', 'معطّل': 'danger', 'محظور وموثق': 'danger', 'false': 'danger', 'denied': 'danger', 'failed': 'danger',
    'عالٍ': 'danger', 'متوسط': 'warning', 'منخفض': 'neutral',
    'VIP': 'accent', 'Gold': 'warning', 'Free': 'neutral',
};
export const chipClass = (v) => TONE_CLASS[STATUS_TONES[String(v)] ?? 'secondary'];

const toast = (msg) => window.dispatchEvent(new CustomEvent('imperial:toast', { detail: msg }));
const isOn = (v) => !(v === false || v === 'false' || v === '0' || v === 0);

/** POST/PATCH/DELETE helper that surfaces refusals as a toast */
async function call(url, opts) {
    const res = await api(url, opts);
    if (!res.ok) toast(res.data.error || 'تعذّر تنفيذ الإجراء.');
    return res;
}

/* ---------------- CSV parsing (preview before commit) ---------------- */
function splitLine(l) {
    const out = [];
    let cur = '';
    let q = false;
    for (const ch of l) {
        if (ch === '"') q = !q;
        else if (ch === ',' && !q) {
            out.push(cur);
            cur = '';
        } else cur += ch;
    }
    out.push(cur);
    return out;
}
export function parseCsv(text) {
    const lines = text.replace(/^﻿/, '').split(/\r?\n/).filter((l) => l.trim());
    if (lines.length < 2) return [];
    const head = splitLine(lines[0]);
    return lines.slice(1).map((l) => {
        const cells = splitLine(l);
        const row = { id: `im-${Math.random().toString(36).slice(2, 7)}` };
        head.forEach((h, i) => (row[h.trim()] = (cells[i] ?? '').trim()));
        return row;
    });
}

/* ---------------- media helpers ---------------- */
const titleFromFile = (name) => name.replace(/\.[a-z0-9]+$/i, '').replace(/[_-]+/g, ' ').trim();
const formatSize = (b) => (b < 1024 ? `${b} بايت` : b < 1048576 ? `${(b / 1024).toFixed(1)} كيلوبايت` : `${(b / 1048576).toFixed(1)} ميجابايت`);
function detectVideoLink(url) {
    const u = url.trim().toLowerCase();
    if (u.length < 10 || !u.includes('.')) return { error: 'أدخل رابط فيديو صحيحًا (YouTube أو Drive).' };
    if (u.includes('youtube.com') || u.includes('youtu.be')) return 'video-yt';
    if (u.includes('drive.google.') || u.includes('docs.google.')) return 'video-drive';
    return { error: 'الروابط المدعومة حاليًا: YouTube أو Google Drive فقط.' };
}

export default function registerImperial(Alpine) {
    /* ============================ shell ============================ */
    Alpine.data('imperialShell', ({ module, palette }) => ({
        module,
        sidebarOpen: true,
        mobileNav: false,
        paletteOpen: false,
        paletteQ: '',
        notifs: false,
        resetAsk: false,
        resetError: null,
        busy: false,
        toast: null,
        toastTimer: null,
        init() {
            try {
                this.sidebarOpen = localStorage.getItem('imperial-sidebar') !== '0';
            } catch {
                /* ignore */
            }
            this.$watch('sidebarOpen', (v) => {
                try {
                    localStorage.setItem('imperial-sidebar', v ? '1' : '0');
                } catch {
                    /* ignore */
                }
            });
            this.$watch('paletteOpen', (v) => v && (this.paletteQ = '', this.$nextTick(() => this.$refs.paletteInput?.focus())));
            window.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    this.paletteOpen = !this.paletteOpen;
                }
                if (e.key === 'Escape') {
                    this.paletteOpen = false;
                    this.notifs = false;
                }
            });
            window.addEventListener('imperial:toast', (e) => {
                this.toast = e.detail;
                clearTimeout(this.toastTimer);
                this.toastTimer = setTimeout(() => (this.toast = null), 4000);
            });
        },
        get paletteHits() {
            const needle = this.paletteQ.trim();
            const visible = palette.filter((h) => h.kind !== 'وحدة' || h.allowed);
            if (!needle) return visible.slice(0, 12);
            return visible.filter((h) => h.label.includes(needle) || h.sub.includes(needle)).slice(0, 12);
        },
        /* navigate to a module, or log a denied attempt (exactly like the SPA) */
        async go(target, allowed) {
            if (allowed) {
                window.location.href = `/imperial/${target}`;
                return;
            }
            await api(`${API}/denied`, { body: { module: target, action: 'view' } });
            this.paletteOpen = false;
            this.notifs = false;
            toast('صلاحيات حسابك لا تخوّل عرض هذه الوحدة — تم تسجيل المحاولة.');
        },
        async resetWorkspace() {
            this.busy = true;
            const { ok, data } = await api(`${API}/workspace/reset`, {});
            this.busy = false;
            if (!ok) {
                this.resetError = data.error;
                return;
            }
            window.location.href = '/imperial/dashboard';
        },
    }));

    /* ============================ data table ============================ */
    Alpine.data('dataTable', (cfg) => ({
        ...cfg,
        q: '',
        sortKey: null,
        sortDir: 1,
        page: 0,
        selected: [],
        viewing: null,
        deleting: null,
        reason: '',
        adding: false,
        editId: null,
        formVals: {},
        importing: null,
        busy: false,
        PAGE: 6,

        get filtered() {
            const needle = this.q.trim();
            let out = this.rows;
            if (needle) out = out.filter((r) => this.columns.some((c) => String(r[c.key] ?? '').includes(needle)));
            if (this.sortKey) {
                const k = this.sortKey;
                out = [...out].sort((a, b) => {
                    const an = Number(a[k]);
                    const bn = Number(b[k]);
                    const cmp = !isNaN(an) && !isNaN(bn) ? an - bn : String(a[k]).localeCompare(String(b[k]), 'ar');
                    return cmp * this.sortDir;
                });
            }
            return out;
        },
        get pages() {
            return Math.max(1, Math.ceil(this.filtered.length / this.PAGE));
        },
        get pageRows() {
            return this.filtered.slice(this.page * this.PAGE, this.page * this.PAGE + this.PAGE);
        },
        get allChecked() {
            return this.pageRows.length > 0 && this.pageRows.every((r) => this.selected.includes(String(r.id)));
        },
        chipClass,
        isOn,
        sortBy(key) {
            if (this.sortKey === key) this.sortDir = this.sortDir === 1 ? -1 : 1;
            else {
                this.sortKey = key;
                this.sortDir = 1;
            }
        },
        toggleAll(on) {
            const ids = this.pageRows.map((r) => String(r.id));
            this.selected = on ? [...new Set([...this.selected, ...ids])] : this.selected.filter((id) => !ids.includes(id));
        },
        toggleRow(row, on) {
            const id = String(row.id);
            this.selected = on ? [...this.selected, id] : this.selected.filter((x) => x !== id);
        },
        apply(res) {
            if (res.ok && res.data.rows) {
                this.rows = res.data.rows;
                if (this.page >= this.pages) this.page = Math.max(0, this.pages - 1);
            }
            return res.ok;
        },
        async denied(action) {
            await api(`${API}/denied`, { body: { module: this.module, action } });
            toast('إجراء خارج صلاحياتك — تم تسجيل المحاولة في سجل التدقيق.');
        },
        beginForm(row) {
            const init = {};
            this.columns.forEach((c) => (init[c.key] = row ? String(row[c.key] ?? '') : ''));
            this.formVals = init;
            this.editId = row ? String(row.id) : null;
            this.adding = true;
        },
        async submitForm() {
            this.busy = true;
            const vals = { ...this.formVals };
            const res = this.editId
                ? await call(`${API}/records/${this.dataset}/${encodeURIComponent(this.editId)}`, { method: 'PATCH', body: { module: this.module, patch: vals } })
                : await call(`${API}/records/${this.dataset}`, { body: { module: this.module, row: vals } });
            this.busy = false;
            if (this.apply(res)) {
                this.adding = false;
                this.editId = null;
            }
        },
        async toggleStat(row) {
            this.apply(await call(`${API}/records/${this.dataset}/${encodeURIComponent(row.id)}`, { method: 'PATCH', body: { module: this.module, patch: { enabled: !isOn(row.enabled) } } }));
        },
        async transition(row, action, label) {
            this.apply(await call(`${API}/records/${this.dataset}/${encodeURIComponent(row.id)}/transition`, { body: { module: this.module, action, label } }));
        },
        async remove() {
            this.busy = true;
            const ok = this.apply(await call(`${API}/records/${this.dataset}/${encodeURIComponent(this.deleting.id)}`, { method: 'DELETE', body: { module: this.module, reason: this.reason } }));
            this.busy = false;
            if (ok) {
                this.selected = this.selected.filter((id) => id !== String(this.deleting.id));
                this.deleting = null;
            }
        },
        async archiveInstead() {
            await this.transition(this.deleting, 'archive', 'أرشفة بدل الحذف');
            this.deleting = null;
        },
        async bulk(kind) {
            if (this.apply(await call(`${API}/records/${this.dataset}/bulk`, { body: { module: this.module, keys: this.selected, kind } }))) this.selected = [];
        },
        exportRows(fmt) {
            const ids = this.selected.length ? `&ids=${encodeURIComponent(this.selected.join(','))}` : '';
            window.location.href = `${API}/records/${this.dataset}/export?module=${this.module}&format=${fmt}${ids}`;
        },
        onImportFile(e) {
            const f = e.target.files?.[0];
            e.target.value = '';
            if (!f) return;
            f.text().then((t) => (this.importing = parseCsv(t)));
        },
        async commitImport() {
            this.busy = true;
            const ok = this.apply(await call(`${API}/records/${this.dataset}/import`, { body: { module: this.module, rows: this.importing } }));
            this.busy = false;
            if (ok) this.importing = null;
        },
    }));

    /* ============================ approvals ============================ */
    Alpine.data('approvalsPage', (rows) => ({
        rows,
        decision: null,
        noteTxt: '',
        busy: false,
        get pending() {
            return this.rows.filter((a) => a.status === 'بانتظار القرار');
        },
        async decide() {
            this.busy = true;
            const res = await call(`${API}/approvals/${encodeURIComponent(this.decision.id)}/decide`, { body: { type: this.decision.type, note: this.noteTxt } });
            this.busy = false;
            if (res.ok) {
                this.rows = res.data.rows;
                this.decision = null;
                this.noteTxt = '';
            }
        },
    }));

    /* ============================ RBAC matrix ============================ */
    Alpine.data('rbacPage', (matrix, overrides) => ({
        matrix,
        overrides,
        async toggle(role, module) {
            const res = await call(`${API}/rbac/toggle`, { body: { role, module } });
            if (res.ok) {
                this.matrix[role][module] = res.data.allowed;
                this.overrides = res.data.overridesCount ?? this.overrides;
            }
        },
        async resetAll() {
            if ((await call(`${API}/rbac/reset`, {})).ok) window.location.reload();
        },
    }));

    /* ============================ CMS hero ============================ */
    Alpine.data('cmsPage', (initial) => ({
        vals: { ...initial },
        saved: { ...initial },
        busy: false,
        published: false,
        get dirty() {
            return JSON.stringify(this.vals) !== JSON.stringify(this.saved);
        },
        async publish() {
            this.busy = true;
            const res = await call(`${API}/cms`, { body: this.vals });
            this.busy = false;
            if (res.ok) {
                this.saved = { ...this.vals };
                this.published = true;
                setTimeout(() => (this.published = false), 3000);
            }
        },
    }));

    /* ============================ settings ============================ */
    Alpine.data('settingsPage', () => ({
        cfg: { platformName: 'منصة الإمبراطور', simTimePerQuestion: '٤٥ ثانية', weeklyGoalDefault: '٥ محاكيات', sessionHours: '١٢ ساعة', auditRetention: '٩٠ يومًا' },
        saved: false,
        busy: false,
        async save() {
            this.busy = true;
            const res = await call(`${API}/settings`, { body: { cfg: this.cfg } });
            this.busy = false;
            if (res.ok) {
                this.saved = true;
                setTimeout(() => (this.saved = false), 2500);
            }
        },
    }));

    /* ============================ student caps ============================ */
    Alpine.data('capsPage', (rows) => ({
        rows,
        q: '',
        plan: 'all',
        edited: null,
        busy: false,
        form: { simsLimit: '', simUnlimited: false, vault: 'متاح', live: 'متاح', aiLimit: '30' },
        get active() {
            return this.rows.filter((s) => s.status === 'نشط').length;
        },
        get visible() {
            const q = this.q.trim();
            return this.rows.filter((s) => {
                if (this.plan !== 'all' && String(s.plan) !== this.plan) return false;
                if (!q) return true;
                return [s.name, s.email, s.phone].some((v) => String(v ?? '').includes(q));
            });
        },
        planClass(p) {
            return String(p) === 'باقة ملكية VIP' ? TONE_CLASS.accent : String(p) === 'باقة ذهبية' ? TONE_CLASS.warning : TONE_CLASS.neutral;
        },
        simCap(s) {
            if (s.simUnlimited === true || String(s.simsLimit) === '∞') return 'مفتوح غير محدود ∞';
            return `${s.simsUsed} من أصل ${s.simsLimit}`;
        },
        aiCap(s) {
            const n = Number(s.aiLimit ?? 0);
            return !Number.isFinite(n) || n <= 0 ? 'معطل' : `${n} استشارة/يوم`;
        },
        capTone(v) {
            const t = String(v);
            if (t.includes('محظور') || t.includes('معطل')) return 'text-danger';
            if (t.includes('مقيد')) return 'text-warning';
            return 'text-success';
        },
        openEditor(s) {
            this.edited = s;
            const unlimited = s.simUnlimited === true || String(s.simsLimit) === '∞';
            this.form = { simsLimit: unlimited ? '' : String(s.simsLimit), simUnlimited: unlimited, vault: String(s.vault ?? 'متاح'), live: String(s.live ?? 'متاح'), aiLimit: String(Number(s.aiLimit ?? 0)) };
        },
        async patch(s, patch, note) {
            const res = await call(`${API}/records/students/${encodeURIComponent(s.id)}`, { method: 'PATCH', body: { module: 'student-caps', patch, note } });
            if (res.ok) this.rows = res.data.rows;
            return res.ok;
        },
        async saveEditor() {
            const f = this.form;
            const patch = { vault: f.vault, live: f.live, aiLimit: Number(f.aiLimit) || 0, simUnlimited: f.simUnlimited };
            patch.simsLimit = f.simUnlimited ? '∞' : f.simsLimit ? Number(f.simsLimit) : this.edited.simsLimit;
            this.busy = true;
            const ok = await this.patch(this.edited, patch, {
                action: 'تعديل الصلاحيات والسقف',
                detail: `${this.edited.name} ← محاكي: ${f.simUnlimited ? '∞' : patch.simsLimit} · خزينة: ${f.vault} · بث: ${f.live} · AI: ${patch.aiLimit}`,
            });
            this.busy = false;
            if (ok) this.edited = null;
        },
        toggleSuspend(s) {
            const active = s.status === 'نشط';
            this.patch(s, { status: active ? 'موقوف' : 'نشط' }, { action: active ? 'تعليق الحساب' : 'تفعيل الحساب', detail: String(s.name) });
        },
    }));

    /* ============================ media center ============================ */
    Alpine.data('mediaPage', (rows, actor) => ({
        rows,
        q: '',
        kind: 'all',
        cat: 'all',
        playMsg: null,
        deleting: null,
        reason: '',
        editing: null,
        pending: null,
        file: null,
        busy: false,
        formError: null,
        linkInput: '',
        linkError: null,
        form: { title: '', category: 'شروحات مرئية', uploader: actor, description: '', duration: '', url: '' },
        get visible() {
            const q = this.q.trim();
            const map = { mp4: 'video-mp4', youtube: 'video-yt', drive: 'video-drive' };
            return this.rows.filter((r) => {
                if (this.kind !== 'all' && r.kind !== map[this.kind]) return false;
                if (this.cat !== 'all' && String(r.category) !== this.cat) return false;
                if (!q) return true;
                return [r.title, r.uploader, r.description, r.notes].some((v) => String(v ?? '').includes(q));
            });
        },
        isVideo: (r) => String(r.kind).startsWith('video'),
        categoryClass(c) {
            const v = String(c);
            if (v === 'تجميعات 120') return TONE_CLASS.primary;
            if (v === 'حقائب تدريبية') return TONE_CLASS.secondary;
            if (v === 'صور أسئلة') return TONE_CLASS.warning;
            return TONE_CLASS.accent;
        },
        note(action, detail, result = 'success') {
            api(`${API}/note`, { body: { module: 'media-center', action, detail, result } });
        },
        startEdit(row) {
            this.editing = row;
            this.file = null;
            this.pending = null;
            this.formError = null;
            if (row === 'new') {
                this.form = { title: '', category: 'شروحات مرئية', uploader: actor, description: '', duration: '', url: '' };
            } else {
                this.form = { title: String(row.title ?? ''), category: String(row.category ?? 'شروحات مرئية'), uploader: String(row.uploader ?? actor), description: String(row.description ?? row.notes ?? ''), duration: String(row.duration ?? ''), url: String(row.url ?? '') };
            }
        },
        cancelEdit() {
            this.editing = null;
            this.pending = null;
            this.file = null;
        },
        onFilePicked(e, source) {
            const f = e.target.files?.[0];
            e.target.value = '';
            if (!f) return;
            this.startEdit('new');
            this.file = f;
            this.pending = { kind: source === 'video' ? 'video-mp4' : 'file', size: formatSize(f.size) };
            this.form.title = titleFromFile(f.name);
            this.form.category = source === 'video' ? 'شروحات مرئية' : 'تجميعات 120';
            this.note('تحليل ملف محلي', `${f.name} (${formatSize(f.size)})`);
        },
        insertLink() {
            const detected = detectVideoLink(this.linkInput);
            if (typeof detected !== 'string') {
                this.linkError = detected.error;
                return;
            }
            this.linkError = null;
            const url = this.linkInput.trim();
            this.startEdit('new');
            this.pending = { kind: detected, size: '—' };
            this.form.title = titleFromFile(url.split('/').pop() || 'video');
            this.form.url = url;
            this.note('كشف رابط فيديو', detected === 'video-yt' ? 'YouTube' : 'Drive');
            this.linkInput = '';
        },
        async saveEdit() {
            this.busy = true;
            this.formError = null;
            const fd = new FormData();
            Object.entries(this.form).forEach(([k, v]) => v !== '' && v !== null && fd.append(k, v));
            if (this.file) fd.append('file', this.file);
            const url = this.editing === 'new' ? `${API}/media` : `${API}/media/${encodeURIComponent(this.editing.id)}`;
            const res = await api(url, { form: fd });
            this.busy = false;
            if (!res.ok) {
                this.formError = res.data.error || 'تعذّر الحفظ.';
                return;
            }
            this.rows = res.data.rows;
            this.cancelEdit();
        },
        play(row) {
            if (row.url) {
                window.open(row.url, '_blank', 'noopener,noreferrer');
                this.note('تشغيل فيديو خارجي', String(row.title));
                return;
            }
            this.playMsg = `معاينة ${row.title} غير متاحة — لم يُرفق ملف فيديو بهذا السجل بعد`;
            this.note('طلب معاينة MP4 محلي', String(row.title), 'denied');
        },
        copyLink(row) {
            const text = row.url ? new URL(row.url, window.location.origin).href : String(row.title);
            navigator.clipboard?.writeText(text).catch(() => undefined);
            this.note('نسخ', `نسخ: ${row.title}`);
        },
        async remove() {
            this.busy = true;
            const res = await call(`${API}/records/media/${encodeURIComponent(this.deleting.id)}`, { method: 'DELETE', body: { module: 'media-center', reason: this.reason } });
            this.busy = false;
            if (res.ok) {
                this.rows = res.data.rows;
                this.deleting = null;
            }
        },
        archiveInstead() {
            this.note('أرشفة بدل الحذف', String(this.deleting.title));
            this.deleting = null;
        },
    }));

    /* ============================ SEO ============================ */
    Alpine.data('seoPage', (pages, overrides) => ({
        pages,
        overrides,
        sel: 'home',
        draft: {},
        saved: false,
        busy: false,
        get display() {
            const base = { ...this.pages[this.sel] };
            const o = this.overrides?.[this.sel] ?? {};
            Object.entries(o).forEach(([k, v]) => {
                if (v !== undefined && v !== '' && !(Array.isArray(v) && !v.length)) base[k] = v;
            });
            const kw = Array.isArray(base.secondaryKeywords) ? base.secondaryKeywords.join(', ') : String(base.secondaryKeywords ?? '');
            return {
                title: this.draft.title ?? base.title,
                description: this.draft.description ?? base.description,
                primaryKeyword: this.draft.primaryKeyword ?? base.primaryKeyword,
                secondaryKeywords: this.draft.secondaryKeywords ?? kw,
                robots: this.draft.robots ?? base.robots,
                canonical: this.pages[this.sel].canonical,
                ogImage: base.ogImage,
            };
        },
        resetDraft() {
            this.draft = {};
            this.saved = false;
        },
        async save() {
            this.busy = true;
            const res = await call(`${API}/seo`, { body: { page: this.sel, ...this.draft } });
            this.busy = false;
            if (res.ok) {
                if (res.data.overrides) this.overrides = res.data.overrides;
                this.draft = {};
                this.saved = true;
                setTimeout(() => (this.saved = false), 3000);
            }
        },
    }));

    /* ============================ marketing / AdSense ============================ */
    Alpine.data('marketingPage', (marketing) => ({
        m: marketing,
        pub: marketing.publisherId ?? '',
        error: null,
        saved: false,
        valid: (id) => /^ca-pub-\d{12,18}$/.test(String(id ?? '').trim().toLowerCase()),
        placementOn(key) {
            return this.m.placements?.[key] !== false;
        },
        async save(patch) {
            const res = await call(`${API}/marketing`, { body: patch });
            if (res.ok) this.m = res.data.marketing;
            return res;
        },
        togglePlacement(key) {
            this.save({ placements: { ...(this.m.placements ?? {}), [key]: !this.placementOn(key) } });
        },
        async applyPub() {
            if (this.pub.trim() && !this.valid(this.pub)) {
                this.error = 'صيغة ID ناشر غير صحيحة — يجب أن تكون «ca-pub- ثم 12–18 رقمًا». لا تُحفظ قيم غير صالحة.';
                return;
            }
            this.error = null;
            const res = await this.save({ publisherId: this.pub.trim() });
            if (res.ok) {
                this.saved = true;
                setTimeout(() => (this.saved = false), 2400);
            } else {
                this.error = res.data.error;
            }
        },
    }));

    /* ============================ content manager ============================ */
    Alpine.data('contentManager', (rows, groups) => ({
        rows,
        groups,
        sel: 'about',
        q: '',
        edited: null,
        adding: false,
        deleting: null,
        reason: '',
        saved: false,
        busy: false,
        form: { title: '', description: '', keywords: '', robots: 'index,follow', ogImage: '' },
        get visible() {
            const query = this.q.trim();
            if (!query) return this.rows;
            return this.rows.filter((r) => [r._title, r._sub, r.type ?? '', r.kind ?? ''].some((v) => String(v ?? '').includes(query)));
        },
        group() {
            return this.groups.find((g) => g.id === this.sel);
        },
        groupLabel() {
            return this.group()?.label ?? this.sel;
        },
        groupTone() {
            return TONE_CLASS[this.group()?.tone ?? 'neutral'];
        },
        /* refresh one dataset slice of the aggregated list from a server response */
        merge(dataset, fresh) {
            const decorate = (r) => ({ ...r, _dataset: dataset, _title: String(r.title ?? r.name ?? r.subject ?? '#'), _sub: String(r.subtitle ?? r.description ?? r.notes ?? '') });
            const others = this.rows.filter((r) => r._dataset !== dataset);
            const order = ['contents', 'questions', 'resources', 'media', 'testimonials'];
            this.rows = [...others, ...fresh.map(decorate)].sort((a, b) => order.indexOf(a._dataset) - order.indexOf(b._dataset));
        },
        beginEdit(row) {
            this.edited = row;
            this.form = row
                ? { title: String(row.title || row._title || ''), description: String(row.description || row._sub || ''), keywords: String(row.title || ''), robots: 'index,follow', ogImage: String(row.image ?? row.url ?? '') }
                : { title: '', description: '', keywords: '', robots: 'index,follow', ogImage: '' };
            this.adding = true;
        },
        async save() {
            this.busy = true;
            const today = new Date().toISOString().slice(0, 10);
            let res;
            let dataset;
            if (this.edited) {
                dataset = this.edited._dataset;
                const titleKey = 'title' in this.edited || dataset === 'contents' || dataset === 'media' ? 'title' : 'name';
                const patch = { [titleKey]: this.form.title, updated: today };
                if (this.form.description && 'description' in this.edited) patch.description = this.form.description;
                res = await call(`${API}/records/${dataset}/${encodeURIComponent(this.edited.id)}`, {
                    method: 'PATCH',
                    body: { module: 'content-manager', patch, note: { action: 'تعديل محتوى الصفحة', detail: `${this.edited._title} — تعديل title/description` } },
                });
            } else {
                dataset = 'contents';
                res = await call(`${API}/records/contents`, {
                    body: {
                        module: 'content-manager',
                        row: { title: this.form.title, subtitle: '', description: this.form.description, kind: this.sel, status: 'منشور', owner: 'الإدارة العليا', updated: today },
                        note: { action: 'إنشاء قسم جديد', detail: `${this.form.title} — نوع: ${this.sel}` },
                    },
                });
            }
            this.busy = false;
            if (res.ok) {
                this.merge(dataset, res.data.rows);
                this.adding = false;
                this.saved = true;
                setTimeout(() => (this.saved = false), 2400);
            }
        },
        async toggleVisible(row) {
            const next = row.enabled === false;
            const res = await call(`${API}/records/${row._dataset}/${encodeURIComponent(row.id)}`, {
                method: 'PATCH',
                body: { module: 'content-manager', patch: { enabled: next }, note: { action: next ? 'إظهار قسم' : 'إخفاء قسم', detail: row._title } },
            });
            if (res.ok) this.merge(row._dataset, res.data.rows);
        },
        async remove() {
            const row = this.deleting;
            this.busy = true;
            const res = await call(`${API}/records/${row._dataset}/${encodeURIComponent(row.id)}`, {
                method: 'DELETE',
                body: { module: 'content-manager', reason: this.reason, note: { action: 'حذف محتوى', detail: `${row.title || row._title} — سبب: ${this.reason}` } },
            });
            this.busy = false;
            if (res.ok) {
                this.merge(row._dataset, res.data.rows);
                this.deleting = null;
                this.reason = '';
            }
        },
    }));
}
