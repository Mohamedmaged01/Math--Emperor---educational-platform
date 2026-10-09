# PHASE 11 — Privacy Impact Assessment (Addendum §3.12)

> Date: 2026-09-25 · Status: **INITIAL — PENDING GOVERNANCE APPROVAL** (agent must not self-approve, §AO).

## 1. Processing Activity
| Activity | Purpose | Data Categories | Data Subjects | Risks | Controls | Residual Risks | Third Parties | AI Processing | Retention | Security Measures | Open Questions | Approval Status |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Landing page rendering | Marketing | None personal (static content, device theme pref) | Anonymous visitors | Track-free by design: no analytics/tracker in this build | No cookies set by app; localStorage only for theme | Low | Fonts CDN (Google Fonts) | No | n/a | HTTPS (deploy-level), no form collection | Fonts CDN policy confirmation at deploy | Pending |
| Simulator demo | Engagement | Ephemeral in-memory answers | Anonymous visitors | None persisted | Nothing stored; no telemetry | Low | None | No | Page lifetime | Client-only | Post-up logout binding | Pending |
| (⏳) IAM & sessions (Ph2–3) | AuthN/AuthZ | Identity, credentials-hash, sessions, RBAC | Students/staff/parents | Credential abuse, IDOR, tenant escape | argon2id, JWT/OIDC design, RLS, RBAC, audit, rate-limits, lockout (blueprints) | Medium until executed & tested | Auth libs (TBD) | No | TBD policy | TLS, hashing, RLS, audit | MFA policy scope | Pending |
| (⏳) Learning & assessment (Ph4–6) | Education | Academic records, attempts, mastery, events | Students | Educational-record exposure, parent-IDOR | Relationship+tenant+permission checks; event validation; immutable results | Medium | None default | Assistive only | TBD tiers | RLS, validation, DSR workflow | Parent consent model vs KSA context | Pending |
| (⏳) AI tutoring (Ph7) | Learning support | Minimized context, messages | Students | Prompt leakage, over-retention | Context isolation, PII minimization, retention tiers, no keys in prompts | Medium-high until provider DPA | Provider (TBD, DPA required) | Yes — core | Configurable | Provider allow-list, redaction, audit | Provider data-residency (KSA/PDPL) | Pending |
| (⏳) Billing (Ph10) | Commerce | Payment refs, invoices | Payers/tenants | Financial exposure | Tokenized provider, no PAN storage, RLS, least privilege | Low-medium | Payment gateway, tax/ZATCA | No | Legal retention | Webhook signing, idempotency | ZATCA fields scope | Pending |

## 2. Cross-cutting rows
- **Risks covered by Risk Register:** RISK-GOV-001…004; privacy-specific follow-ups to be created under `RISK-PRV-*` during Phase 11 execution (blocked now).
- **Residual-risk posture:** all unresolved items are governed by OPEN status + owner + review date; **no self-acceptance of privacy risks will occur (§AO)**.
- **Approval Status:** Pending stakeholder/governance (this PIA is an input to Phase-11 re-execution, not a substitute for it).
