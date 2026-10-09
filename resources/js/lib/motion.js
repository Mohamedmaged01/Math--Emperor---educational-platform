/**
 * Motion layer — replaces framer-motion's whileInView / animate / useScroll.
 * Elements opt in with data-reveal | data-bar | data-hbar | data-ring | data-draw | data-pop;
 * an IntersectionObserver adds `.is-in` once, and a MutationObserver picks up
 * nodes that Alpine renders later (x-for / x-if / x-teleport).
 */
const SELECTOR = '[data-reveal],[data-bar],[data-hbar],[data-ring],[data-draw],[data-pop]';

export function initMotion() {
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const io = new IntersectionObserver(
        (entries) => {
            for (const e of entries) {
                if (e.isIntersecting) {
                    e.target.classList.add('is-in');
                    io.unobserve(e.target);
                }
            }
        },
        { rootMargin: '0px 0px -60px 0px', threshold: 0.01 },
    );

    const watch = (root) => {
        if (!(root instanceof Element)) return;
        if (root.matches(SELECTOR)) reduce ? root.classList.add('is-in') : io.observe(root);
        root.querySelectorAll(SELECTOR).forEach((el) => (reduce ? el.classList.add('is-in') : io.observe(el)));
    };

    watch(document.body);
    new MutationObserver((muts) => muts.forEach((m) => m.addedNodes.forEach(watch))).observe(document.body, {
        childList: true,
        subtree: true,
    });

    /* scroll progress bar */
    const bar = document.querySelector('[data-scroll-progress]');
    if (bar) {
        let raf = null;
        const update = () => {
            raf = null;
            const max = document.documentElement.scrollHeight - window.innerHeight;
            bar.style.setProperty('--progress', max > 0 ? String(window.scrollY / max) : '0');
        };
        update();
        window.addEventListener('scroll', () => raf ?? (raf = requestAnimationFrame(update)), { passive: true });
        window.addEventListener('resize', update, { passive: true });
    }
}

/** spring-ish follower for the hero mouse parallax */
export function createParallax(target) {
    let tx = 0;
    let ty = 0;
    let x = 0;
    let y = 0;
    let running = false;

    const step = () => {
        x += (tx - x) * 0.08;
        y += (ty - y) * 0.08;
        target.style.setProperty('--img-x', `${(-x * 24).toFixed(2)}px`);
        target.style.setProperty('--img-y', `${(-y * 18).toFixed(2)}px`);
        target.style.setProperty('--badge-x', `${(x * 40).toFixed(2)}px`);
        target.style.setProperty('--badge-y', `${(y * 28).toFixed(2)}px`);
        if (Math.abs(tx - x) > 0.001 || Math.abs(ty - y) > 0.001) requestAnimationFrame(step);
        else running = false;
    };

    return (nx, ny) => {
        tx = nx;
        ty = ny;
        if (!running) {
            running = true;
            requestAnimationFrame(step);
        }
    };
}
