import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import intersect from '@alpinejs/intersect';

import { initMotion } from './lib/motion';
import registerSite from './components/site';
import registerImperial from './components/imperial';

Alpine.plugin(collapse);
Alpine.plugin(intersect);

/* ---------------- theme (light · dark · system) ---------------- */
const THEME_KEY = 'math-emperor-theme';
const THEME_LABELS = { light: 'الوضع الفاتح', dark: 'الوضع الداكن', system: 'حسب النظام' };
const media = () => window.matchMedia('(prefers-color-scheme: dark)');

Alpine.store('theme', {
    theme: (() => {
        try {
            const t = localStorage.getItem(THEME_KEY);
            return t === 'dark' || t === 'system' || t === 'light' ? t : 'light';
        } catch {
            return 'light';
        }
    })(),
    get label() {
        return THEME_LABELS[this.theme];
    },
    init() {
        this.apply();
        media().addEventListener('change', () => this.theme === 'system' && this.apply());
    },
    apply() {
        const dark = this.theme === 'dark' || (this.theme === 'system' && media().matches);
        document.documentElement.classList.toggle('dark', dark);
        try {
            localStorage.setItem(THEME_KEY, this.theme);
        } catch {
            /* private mode — non-fatal */
        }
    },
    cycle() {
        this.theme = this.theme === 'light' ? 'dark' : this.theme === 'dark' ? 'system' : 'light';
        this.apply();
    },
});

/* ---------------- scroll lock shared by every modal / window ---------------- */
Alpine.store('lock', {
    ids: new Set(),
    set(id, on) {
        if (on) this.ids.add(id);
        else this.ids.delete(id);
        document.body.style.overflow = this.ids.size ? 'hidden' : '';
    },
});

/* ---------------- full-screen windows: one at a time ---------------- */
Alpine.store('windows', {
    closeAll() {
        window.dispatchEvent(new CustomEvent('emperor:close-windows'));
    },
});

/* ---------------- portals: signed-in users go straight to their space ---------------- */
Alpine.store('portal', {
    open(kind) {
        const auth = window.EMPEROR?.auth;
        if (auth) {
            window.location.href = auth.role === 'STAFF' ? window.EMPEROR.routes.imperial : window.EMPEROR.routes.student;
            return;
        }
        window.dispatchEvent(new CustomEvent(kind === 'imperial' ? 'emperor:open-imperial' : 'emperor:open-student'));
    },
});

registerSite(Alpine);
registerImperial(Alpine);

window.Alpine = Alpine;
Alpine.start();
initMotion();
