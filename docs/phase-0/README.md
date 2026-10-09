# MATH EMPEROR — منصة إمبراطور الرياضيات
# PHASE 0 — Architecture Audit, Codebase Discovery & Technical Foundation

> **Status:** PHASE 0 ONLY · Discovery + Documentation + Safe Foundation
> **Rule №1 (Golden Rule):** DO NOT BREAK EXISTING FEATURES. Every change must be documented, justified, reversible, non-breaking, and covered by a regression check.
> **NOT FOUND policy:** any fact absent from this repository is recorded as `NOT FOUND`. Nothing is invented.

---

## 0. Document Index

| # | Document | Covers |
|---|----------|--------|
| 01 | [Codebase Audit & Inventory](./01-codebase-audit-and-inventory.md) | §0.3 Audit · §0.4 Component Inventory · §0.5 Current Architecture |
| 02 | [Target Architecture](./02-target-architecture.md) | §0.6 Target · §0.7 Style · §0.8 SaaS · §0.9 Academic · §0.10 Learning · §0.11 AI · §0.12 AI Governance · §0.13 Analytics · §0.19 Frontend Structure |
| 03 | [Database Blueprint](./03-database-blueprint.md) | §0.14 Domains/Tables · §0.15 Rules |
| 04 | [API Blueprint & RBAC](./04-api-blueprint-and-rbac.md) | §0.16 API · §0.17 Response Standard · §0.18 RBAC |

---

## 1. Executive Summary (ملخص تنفيذي)

**Reality check (verified, not assumed):** the current repository is a **single-page marketing/landing application** (React 19 SPA) for the Qudurat/Tahsili platform. It renders marketing sections and one fully interactive client-side Qiyas-style simulator. It has **no backend, no API calls, no database, no authentication, no router, no persistence layer, no tests and no CI** in this snapshot.

Many components referenced in the master plan (Admin Dashboard, Modals suite, Live Classes, Vault, Calculator, AI Tutor…) are **NOT FOUND** in this repository snapshot (full matrix in Doc 01 §4.2). They are therefore treated as **planned future features**, never as existing ones, and nothing was deleted or disabled.

**Target:** evolve into a multi-tenant Saudi EdTech SaaS — **Modular Monolith + API First**, adaptive learning engine + governed AI engine + learning analytics — per Docs 02–04.

**Phase 0 output:** documentation only. Zero source files were modified, added, or removed.

---

## 2. Phase Gates (بوابات المرحلة)

| Gate | Requirement | Evidence | Status |
|------|-------------|----------|--------|
| **G1** | Full codebase audit performed against the real repository (no guessing) | Bash audit (file tree, dependency grep, configs), `package.json`, `vite.config.ts` reads | ✅ PASS |
| **G2** | Complete component inventory per §0.4 schema, incl. NOT FOUND matrix | Doc 01 §4 | ✅ PASS |
| **G3** | Current architecture mapped + coupling/risk register | Doc 01 §5 | ✅ PASS |
| **G4** | Target architecture documented (layers, modules, multi-tenancy, academic/learning/AI/analytics models, frontend structure) | Doc 02 | ✅ DOCUMENTED — ⏳ PENDING STAKEHOLDER APPROVAL |
| **G5** | Database blueprint + rules documented (not implemented) | Doc 03 | ✅ DOCUMENTED — ⏳ PENDING STAKEHOLDER APPROVAL |
| **G6** | API blueprint + response standard + RBAC catalog documented | Doc 04 | ✅ DOCUMENTED — ⏳ PENDING STAKEHOLDER APPROVAL |
| **G7** | Regression baseline: existing app still builds; zero features deleted/disabled | `npm run build` ✅ (post-documentation); git diff = `docs/` only | ✅ PASS |

### Exit Condition — Phase 0 → Phase 1

Phase 1 is **BLOCKED** until ALL of the following hold:

1. G1–G3 ✅ (done).
2. **G4, G5, G6 explicitly approved** by the accountable stakeholder (architecture sign-off).
3. G7 regression baseline re-verified immediately before Phase 1 starts.
4. Phase 1 scope written down and traceable to these documents.

> Creating these files does **not** by itself constitute Phase 0 completion — completion requires the gate approvals above.

---

## 3. Change Log (Phase 0)

| Date | Change | Type | Breaks existing? |
|------|--------|------|------------------|
| Phase 0 | Added `docs/phase-0/*` (5 documents) | Documentation | No — additive only |
