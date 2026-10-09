# 01 — Codebase Audit, Inventory & Current Architecture

تدقيق قاعدة الكود الحالية — موثق من المستودع الفعلي، بدون تخمين.

---

## 1. §0.3 — Codebase Audit (verified facts)

| Category | Finding | Evidence |
|----------|---------|----------|
| Framework | **React 19.2.6** SPA (`react` + `react-dom`) | `package.json` |
| Runtime | Browser SPA (client-rendered). No SSR/Node runtime in repo | `src/main.tsx` mounts `#root` |
| Language | **TypeScript 5.9.3** (`.tsx`/`.ts` only) | file tree |
| Build Tool | **Vite 7.3.2** + `@vitejs/plugin-react` 5.1.1 + `vite-plugin-singlefile` 2.3.0 (inlines all assets into one `dist/index.html`) | `vite.config.ts`, build log |
| Package Manager | **npm** (`package-lock.json` present) | file tree |
| Styling | **Tailwind CSS 4.1.17** via `@tailwindcss/vite` 4.1.17, CSS-first config (`@theme` tokens in `src/index.css`); no `tailwind.config.*` | `src/index.css` |
| Animation | **framer-motion 12** (scroll progress, parallax MotionValues, AnimatePresence) | imports across components |
| Icons | **lucide-react** (+ 4 hand-written inline brand SVGs in Footer) | `Footer.tsx`, imports |
| Class utilities | `clsx` + `tailwind-merge` via `cn()` | `src/utils/cn.ts` |
| Existing dependencies | `react, react-dom, framer-motion, lucide-react, clsx, tailwind-merge` (prod); `vite, @vitejs/plugin-react, @tailwindcss/vite, tailwindcss, typescript, vite-plugin-singlefile, @types/*` (dev) | `package.json` |
| Existing components | 14 (see §4.1) | `find src -type f` |
| Existing routes | **NONE — single page**; navigation = in-page anchor hash links (`#features`, `#simulator`, `#plans`, `#testimonials`, `#pricing`, `#faq`, `#top`) | grep: no `react-router`/`useNavigate` |
| Existing state management | **React local state only** (`useState`, `useRef`); `framer-motion` MotionValues; `useScroll`. No global store, no context, no zustand/redux | grep: NO MATCHES |
| Existing API calls | **NOT FOUND** (no `fetch`/`axios`/`XHR`) | grep: NO MATCHES |
| Existing data sources | Static mock data in `src/data/content.ts` (Arabic content: questions, plans, testimonials, pricing, FAQs, nav) | file read |
| Existing mock data | Same as above — `quizQuestions(3)`, `topics(10)`, `stats(4)`, `studyPlans(3)`, `weekSchedule(7)`, `testimonials(6)`, `pricing(3)`, `faqs(6)`, `navLinks(6)` | `content.ts` |
| Existing authentication | **NOT FOUND** | grep |
| Existing admin authentication | **NOT FOUND** | grep |
| Existing persistence | **NOT FOUND** (no `localStorage`/`sessionStorage`/`indexedDB`, no cookies) | grep: NO MATCHES |
| Existing database integration | **NOT FOUND** | grep |
| Existing environment variables | **NOT FOUND** (no `.env*`, no `import.meta.env` usage) | file tree + grep |
| Existing deployment configuration | **NOT FOUND** in repo (build emits single-file `dist/index.html`; host config lives outside repo) | file tree |
| Existing tests | **NOT FOUND** (no test runner, no `*.test.*`) | file tree |
| Existing linting | **NOT FOUND** in repo (no eslint config; editor-side lint provided by the IDE agent ran during authoring, not part of repo) | file tree |
| Existing formatting | **NOT FOUND** (no prettier config) | file tree |
| Existing CI/CD | **NOT FOUND** (no `.github/`, no pipelines) | file tree |
| Existing security configuration | **NOT FOUND** (no CSP, no headers config, no auth policies, no secrets handling) | file tree |
| Path alias | `@` → `src` configured but currently unused | `vite.config.ts` |
| i18n/a11y | `<html lang="ar" dir="rtl">`, Cairo/Tajawal fonts via Google Fonts, Arabic-first content | `index.html` |

---

## 2. Source Tree (verified)

```text
index.html                  RTL shell, fonts, meta/OG
vite.config.ts              react + tailwind + singlefile + "@" alias
src/main.tsx                entry → <App/>
src/index.css               Tailwind 4 theme tokens + custom keyframes/utilities
src/App.tsx                 composition root + scroll progress bar
src/utils/cn.ts             clsx + tailwind-merge
src/data/content.ts         ALL mock content (single static data source)
src/components/             Navbar, Hero, Marquee, Stats, Features, SectionHeading,
                            Simulator, Plans, Testimonials, Pricing, FAQ, FinalCTA, Footer
docs/phase-0/               Phase 0 documentation (added in this phase — additive only)
```

---

## 3. §0.4 — Existing Component Inventory

### 3.1 Detailed per-component records

**App**
```text
Path: src/App.tsx
Purpose: Composition root; renders Navbar + 12 sections + Footer; golden scrollbar progress.
Inputs: none (root). Outputs: full DOM tree.
State: none (scroll progress via framer useScroll/useSpring — derived, not state).
Dependencies: framer-motion; every section component.
API Dependencies: none. Data Dependencies: none directly.
Navigation Dependencies: defines anchor targets consumed by Navbar (#features…).
Business Rules: section ordering of the marketing funnel.
Risk: MEDIUM — single composition point; any reorder affects funnel only, not logic.
Migration Status: Documented (P0). Keep as composition root; later becomes <LandingPage/> feature route.
```

**Navbar**
```text
Path: src/components/Navbar.tsx
Purpose: Sticky header; logo, anchor links, CTA, mobile menu; glass style on scroll.
Inputs: navLinks (data). Outputs: hash navigation; menu toggle.
State: scrolled(bool), open(bool).
Dependencies: framer-motion, lucide-react, data/content, utils/cn.
API/Data/Navigation: static links → page anchors.
Business Rules: menu closes on link click; CTA → #pricing.
Risk: LOW. Migration Status: Documented — maps to shared/components/layout/Navbar in target structure.
```

**Hero**
```text
Path: src/components/Hero.tsx
Purpose: Conversion hero; parallax mock exam card; social proof; CTAs.
Inputs: mouse position (parallax MotionValues). Outputs: CTA hash navigation.
State: none (MotionValues only).
Dependencies: framer-motion (useMotionValue/useSpring/useTransform), lucide icons.
API/Data: inline Arabic copy only.
Business Rules: —. Risk: MEDIUM-HIGH (conversion-critical visuals).
Migration Status: Documented — maps to features/landing/components/Hero.
```

**Marquee**
```text
Path: src/components/Marquee.tsx
Purpose: Infinite topics ticker (duplicated track, CSS keyframes).
Inputs: topics(data). Outputs: none. State: none.
Dependencies: data/content; CSS .marquee-track.
Risk: LOW. Migration Status: Documented — features/landing.
```

**Stats**
```text
Path: src/components/Stats.tsx
Purpose: Counters animate on view (rAF easing) — 142K+ students etc.
Inputs: stats(data). Outputs: none.
State: display(number) per Counter + useInView.
Dependencies: framer-motion useInView, data/content.
Risk: MEDIUM (custom rAF counter — regression-test animation).
Migration Status: Documented — features/landing.
```

**Features**
```text
Path: src/components/Features.tsx
Purpose: Bento grid of platform features w/ inline micro-visuals (TimerVisual, BankVisual, ChartVisual, leaderboard).
Inputs: none (internal cards array). Outputs: none.
State: none. Dependencies: framer-motion, lucide, SectionHeading.
Risk: MEDIUM. Migration Status: Documented — split micro-visuals into components/library later (no move in Phase 0).
```

**SectionHeading**
```text
Path: src/components/SectionHeading.tsx
Purpose: Shared animated section header (badge/title/goldTitle/desc).
Inputs: props. Outputs: heading UI. State: none.
Risk: LOW — already the first true reusable. Migration Status: Documented — promote to shared/components.
```

**Simulator**  ← the existing Qiyas Simulator (plan name: `QiyasSimulator`)
```text
Path: src/components/Simulator.tsx
Purpose: Interactive Qiyas-style mini exam: idle → running → result state machine.
Inputs: quizQuestions(data). Outputs: score ring, stats, CTA navigation (#pricing).
State: phase, index, selected, timeLeft, score, streak, bestStreak, totalTime + setInterval ref.
Dependencies: framer-motion, lucide, data/content, utils/cn, SectionHeading.
API Dependencies: none. Data Dependencies: quizQuestions (3 items, hardcoded).
Navigation: CTA anchors only.
Business Rules: per-question countdown; timeout ⇒ fail (selected=-1); no back-navigation; scoring & accuracy; streak tracking; explanation reveal post-answer.
Risk: HIGH — timer lifecycle + scoring correctness; must be protected by regression tests before ANY refactor.
Migration Status: Documented — future features/assessment/simulator with question API + attempt persistence (Phase 2+).
```

**Plans**  ← plan name: `StudyPlans`
```text
Path: src/components/Plans.tsx
Purpose: 3 smart plans (selectable) + weekly schedule preview with auto-adjust note.
Inputs: studyPlans, weekSchedule (data). Outputs: CTA (#pricing).
State: active(plan index). Dependencies: framer-motion, lucide, data, cn, SectionHeading.
Business Rules: featured plan highlighted; accordion per selection.
Risk: MEDIUM. Migration Status: Documented — future features/learning/study-plans (real plans Phase 2+).
```

**Testimonials**
```text
Path: src/components/Testimonials.tsx
Purpose: Dual opposite-direction marquee rows of student success cards.
Inputs: testimonials(data). State: none. Risk: LOW.
Migration Status: Documented — future features/cms powered (Phase 3+).
```

**Pricing**  ← plan name relates to `PricingModal` (NOT FOUND — see §4.2)
```text
Path: src/components/Pricing.tsx
Purpose: 3-tier pricing; featured crown tier; payment trust row. Buttons are demo anchors (preventDefault).
Inputs: pricing(data). Outputs: none functional (no checkout).
State: none. Risk: MEDIUM-HIGH (commerce entry; currently demo CTAs only).
Migration Status: Documented — future features/commerce (subscriptions Phase 3+).
```

**FAQ**
```text
Path: src/components/FAQ.tsx
Purpose: Accordion. State: open(index|null). Inputs: faqs(data). Risk: LOW.
Migration Status: Documented — features/cms.
```

**FinalCTA**
```text
Path: src/components/FinalCTA.tsx — big banner CTA → #pricing. State: none. Risk: LOW.
Migration Status: Documented — features/landing.
```

**Footer**
```text
Path: src/components/Footer.tsx — sitemap links + 4 inline brand SVGs + contact info. Risk: LOW.
Migration Status: Documented — components/layout/Footer; move brand SVGs to shared/icons in a later phase.
```

**Supporting modules**
```text
src/main.tsx — entry; mounts App with StrictMode. Risk: LOW.
src/index.css — design tokens (@theme: royal/night palettes, 6 keyframes), grain, glass, gold-text, marquee, scrollbars, .num LTR bidi helper. Risk: MEDIUM (global CSS).
src/data/content.ts — single static content source (Arabic). Risk: MEDIUM (content coupling — fine for landing; must move to CMS/API later).
src/utils/cn.ts — class merge util. Risk: LOW.
```

### 3.2 Requested components — NOT FOUND matrix (per Golden Rule: recorded, never invented)

| Master-plan component | Status in this repo snapshot | Closest existing equivalent |
|---|---|---|
| TrainingSuiteHub | NOT FOUND | Features grid (marketing only) |
| QuickLauncherDock | NOT FOUND | — |
| QiyasSimulator | **EXISTS as `Simulator.tsx`** | src/components/Simulator.tsx |
| QuestionBank | NOT FOUND (no dedicated view) | data `quizQuestions` + Features card |
| CoursesView | NOT FOUND | — |
| LiveClasses | NOT FOUND | — |
| VaultDownloads | NOT FOUND | — |
| StudyPlans | **EXISTS as `Plans.tsx`** (presentation only) | src/components/Plans.tsx |
| WeightedScoreCalculator | NOT FOUND | — |
| ImperialChallenge | NOT FOUND | Features card (leaderboard teaser only) |
| PerformanceAnalytics | NOT FOUND | Hero/Simulator inline stats only |
| PricingModal | NOT FOUND | `Pricing.tsx` section (inline, non-modal) |
| AboutUsModal | NOT FOUND | — |
| ContactUsModal | NOT FOUND | — |
| StudentLoginModal | NOT FOUND (no auth) | — |
| PrivacyPolicyModal | NOT FOUND | — |
| TermsModal | NOT FOUND | — |
| PlatformPolicyModal | NOT FOUND | — |
| StudentPortalModal | NOT FOUND | — |
| AITutorModal | NOT FOUND | — |
| AdminDashboard | NOT FOUND | — |
| AdminAuthModal | NOT FOUND | — |
| Footer | EXISTS | src/components/Footer.tsx |
| FloatingWhatsApp | NOT FOUND | — |
| ScrollToTop | NOT FOUND | — |
| Navbar | EXISTS | src/components/Navbar.tsx |
| Hero | EXISTS | src/components/Hero.tsx |
| App | EXISTS | src/App.tsx |

**Consequence:** all NOT FOUND items are *planned* features for Phases 1+. Phase 0 neither adds nor removes them.

---

## 4. §0.5 — Current Application Architecture

```text
Browser
  └─ index.html  (RTL · Cairo/Tajawal fonts · meta)
       └─ src/main.tsx
            └─ App.tsx  (composition root · golden scroll-progress)
                 ├─ Navbar ──────────────┐
                 ├─ Hero                  │
                 ├─ Marquee               │   hash anchors
                 ├─ Stats                 │   (#features #simulator
                 ├─ Features              │    #plans #testimonials
                 ├─ Simulator (stateful)  │    #pricing #faq #top)
                 ├─ Plans                 │
                 ├─ Testimonials          │
                 ├─ Pricing               │
                 ├─ FAQ                   │
                 ├─ FinalCTA              │
                 └─ Footer ──────────────┘
                        │
       ┌────────────────┼─────────────────┐
       ▼                 ▼                 ▼
 src/data/content.ts   framer-motion     lucide-react
 (static mock data)   (animation only)  (icons)
```

No router · no store · no API · no persistence · no backend.

### Coupling points
1. **`content.ts` is a shared static dependency** of 8 components — change risk is content-only, type-checked by TS inference.
2. **SectionHeading** shared by 5 sections — stable props contract.
3. **Global CSS utilities** (`grain`, `glass`, `gold-text`, `.marquee-track`) consumed implicitly by many components → rename risk.
4. **Anchor-ID contracts** between Navbar/content and section `id` attributes — stringly-typed.
5. **cn()** used by 5 components — stable.

### Duplicate logic / Tech debt
- Marquee pattern duplicated (Marquee + Testimonials implement their own tracks) — acceptable for landing; unify in later phase.
- Hero embeds its own mock exam card separate from Simulator (two representations of "exam UI") — deliberate marketing vs interactive duplication; note for DS unification.
- rAF counter (Stats) is bespoke — replace with tested hook when a hooks layer lands.
- `@` alias unused; some internal visuals in Features should become library components; no design-token TS module (tokens live only in CSS).

### Risk register

| Class | Finding | Severity | Phase-0 action |
|---|---|---|---|
| Security | No CSP/security headers config in repo | Medium | Documented → target headers in Doc 04 §6 (deploy-level, later phase) |
| Security | No auth/secrets handling | N/A (nothing to attack yet) | Designed in Docs 03–04 |
| Scalability | Single-file bundle (~533 kB inlined) grows with every section; no code-splitting | Medium | Documented — Phase 1 introduces router + lazy routes |
| Scalability | Static data can't scale to content ops | High (business) | CMS/API in target (Doc 02) |
| Performance | Google Fonts render-blocking link; no font-display tuning | Low | Later phase (asset pipeline) |
| Performance | Fixed grain overlay + many blurs — GPU cost on low-end mobiles | Low | Visual QA checklist later |
| Maintainability | No tests/lint/CI in repo | High (process) | Phase 1 gate item (foundation) |
| Correctness | Simulator timer scoring is the only real logic — untested | High | Must be first regression-test target before refactor (Phase 1) |

### Regression baseline (snapshot for Golden Rule)
- `npm run build` ✅ green before and after Phase 0 (inlined single file).
- Feature list frozen: 14 components + simulator behaviors (timer, timeout-fail, scoring, streaks, explanation gate, result ring) + nav anchors. Any future change diffs against this document.
