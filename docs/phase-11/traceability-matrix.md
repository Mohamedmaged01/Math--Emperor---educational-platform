# PHASE 11 — Requirement Traceability Matrix (Addendum §4, §7)

> Chain enforced: `Requirement → Risk → Threat → Control → Implementation → API/DB/Module → Test → Evidence → Residual Risk → Approval`.
> Rows marked 🚧 are forward-traceable (planned) — the chain terminates honestly at the first unbuilt link rather than pretending to reach Evidence.

| Requirement | Risk | Threat | Control | Implementation | API / DB / Module | Test | Evidence | Residual Risk | Approval | Status |
| --- | ---- | ------ | ------- | -------------- | ----------------- | ---- | -------- | ------------- | -------- | ------ |
| REQ-GOV-01: Explicit approvals precede execution | RISK-GOV-001 | TM-GOV-01: Unauthorized self-approval | Gate protocol + governance register | `docs/governance/approvals.md` (pending) | governance module (docs) | Gate greps (12 runs on record) | 8 blocked reports | Low (protocol proven) | Pending stakeholder | 🚧 BLOCKED at Implementation |
| REQ-BE-01: Backend + DB + API foundation exists | RISK-GOV-002 | TM-ARCH-01 | Sequential phase plan | Phase 2 (pending) | `/api/v1`, Postgres schema | Phase-2 test suite (planned) | Phase-0 blueprint (design only) | Low after P2 | Pending | 🚧 BLOCKED at Implementation |
| REQ-QA-01: Test/lint/CI infrastructure | RISK-GOV-003 | TM-QA-01: Unverifiable claims | vitest+eslint+CI in Phase 2 plan | Phase 2 tooling (pending) | package scripts, workflows | bootstrap self-test | repo currently lacks scripts (verified) | Medium | Pending | 🚧 BLOCKED at Implementation |
| REQ-EXT-01: Secure provider provisioning | RISK-GOV-004 | TM-EXT-01: Secret exposure | Secrets manager + `.env.example` | Ops provisioning (pending) | env/secrets infra | secret-scan (planned) | repo secret-scan clean (verified: zero secrets) | Medium | Pending | 🚧 BLOCKED at Implementation |
| REQ-PRV-01: Data inventory & classification | RISK-GOV-003 (evidence-gap) | TM-PRV-01: Unknowable data flows | Addendum §3 inventory | `data-inventory.md` ✅ (this file, actual-scope) | docs/phase-11 | Manual review of inventory vs repo | grep/audit citations inside inventory | Low | Pending governance review | ✅ Document layer complete; systems pending |
| REQ-PRV-02: DSR workflow (no direct deletion) | TBD RISK-PRV-* | TM-PRV-02: Unauthorized erasure | Request→Verify→Authorize→Process→Audit→Complete | Phase 11 exec (blocked) | DSR module (planned) | PRIV-019/020 (matrix) | matrix statuses (all BLOCKED, honest) | Medium | Pending | 🚧 BLOCKED at Implementation |
| REQ-ACC-01: Accreditation conflicts identified & decided | None (NF) | TM-ACC-01: Unknown integration conflicts | §2 discovery + register | `accreditation-conflict-register.md` ✅ (N/A — none exist) | docs/phase-11 | §2.1 grep (executed) | grep result recorded in register | Low | Pending governance review | ✅ Discovery complete: NO integration found |
| REQ-SEC-01..n (auth, IDOR, tenant, files, secrets…) | RISK-SEC-* forthcoming | TM-SEC-* per OWASP map | Phase-3+ controls (blueprints) | Phases 3–11 (pending) | IAM/tenancy/storage modules | privacy-test-matrix + security suites | 0 executions possible now | High until built | Pending | 🚧 BLOCKED at Implementation |

**Gate note (AH):** Risk Traceability is forward-complete to the honest stopping point of each row; the chain is not broken by fabrication anywhere. `Approval` column contains no self-approvals by design (§AO).
