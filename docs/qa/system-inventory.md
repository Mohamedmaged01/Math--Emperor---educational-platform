# QA — System Inventory (FULL OPERATIONAL VALIDATION)

> Audit executed 2026-09-25 · Method: repository-wide `find`/`grep`, `package.json` scripts inspection, live build + HTTP smoke (`vite preview` + `curl`). Nothing below is inferred.

## 1. Stack (verified)

| Layer | Reality | Evidence |
|---|---|---|
| Frontend | React 19 + TS + Vite 7 SPA, Tailwind 4, framer-motion | `package.json`, `vite.config.ts` |
| Backend | **NOT FOUND** — no `api/`, `server/`, `backend/`, `migrations/`, `database/` dirs; zero `fetch/axios/ORM` calls in `src/` | repo scan |
| Database | **NOT FOUND** — no driver/ORM/migrations/seeds | repo scan |
| Auth (server) | **NOT FOUND** | repo scan |
| Auth (client demo) | `src/auth/session.ts` — localStorage/sessionStorage session records, explicit inside-code label "LOCAL DEMO" | grep |
| Data persistence | **localStorage only** — 16 calls across: `emperor-admin-v1` (admin demo store), `emperor-portal:*` (student sim records), `emperor-demo-session`, `math-emperor-theme`, CMS override | grep count |
| Test tooling | **NOT FOUND** — scripts are only `dev/build/preview`; no vitest/jest/playwright/cypress | `package.json` |
| CI/CD | **NOT FOUND** | repo scan |
| Live smoke | Build green; `GET / → 200`, `GET /images/saudi-students-hero.jpg → 200`, `GET /images/course-algebra.jpg → 200` | recorded in `final-test-report.md` |

## 2. Functional surfaces that DO exist (client-layer, honestly classified)

| Surface | Behavior | Data layer per §4 |
|---|---|---|
| Landing (Hero/About/Courses/… , RTL, themes) | renders, interactive | static content + CMS override (browser store) |
| Qiyas Simulator (`#simulator`) | full state machine, timer, scoring, explanations | **in-memory — functional** |
| AI Advisor | rule-engine answers from real product data, challenge evaluation | **in-memory — functional** |
| Student portal | sim-record persistence, points/streak update on Simulator completion | **browser localStorage (demo)** |
| Imperial Control Center | dynamic sidebar per role matrix, CRUD UI, CSV import/export (real files), audit stream, approvals, search/palette | **browser localStorage (demo)** |
| Auth gates | login for demo accounts w/ uniform errors | **browser session (demo)** |

## 3. §4 compliance consequence

The prompt's own rule §4 states: any operation relying on Mock Data instead of a database ⇒ **STATUS = FAIL**. The marked "(demo)" rows above constitute exactly that class. Accordingly:

- ✅ These layers are **real and functional as client experiences** and were verified by build + serve + code evidence.
- ❌ They **cannot** satisfy "API → validation → transaction → DB persistence → audit (server)" verification. Required statuses per spec: `FAIL (§4)` for operations presented as CRUD/database, and `NOT IMPLEMENTED / BLOCKED` for every backend-domain module in §7.
