# PHASE 11 — Privacy Test Matrix (Addendum §3.12)

> Compiled 2026-09-25. **Honest status:** every case is `BLOCKED` (never `NOT RUN`→`PASS`) because the systems under test (auth, tenant isolation, AI, storage, exports) exist only as Phase ≥2 designs. Matrix is the executable target for Phase-11 re-run. Testing infra itself (RISK-GOV-003) is part of the unblock work.

| Test ID | Scenario | Component | Method | Expected | Actual | Status | Evidence |
|---|---|---|---|---|---|---|---|
| PRIV-001 | Unauthorized personal-data access | /api/v1 users/profile | Unauthenticated GET/POST | 401 + safe envelope | — | BLOCKED | Pending Phase 2/3 |
| PRIV-002 | Cross-tenant data access (read) | tenant-scoped endpoints | Tenant A token → Tenant B ID (path/query/body) | 404/403, never data | — | BLOCKED | Pending Phase 3 RLS tests |
| PRIV-003 | Parent→student horizontal IDOR | /students/{id}/progress | Parent A → unrelated student ID | DENY (relationship check) | — | BLOCKED | Pending Phase 3/9 |
| PRIV-004 | Parent multi-child scoping | parent dashboard | child A ✓, child B(sibling, linked) ✓, stranger ✗ | allow linked only | — | BLOCKED | Pending Phase 9 |
| PRIV-005 | Deleted/revoked parent relationship | same as PRIV-003 | relationship deleted then repeated | DENY after revocation | — | BLOCKED | Pending Phase 3 |
| PRIV-006 | Teacher→student isolation | teacher cohort APIs | teacher→student outside cohort | DENY | — | BLOCKED | Pending Phase 3/9 |
| PRIV-007 | Admin overreach on personal data | admin listing endpoints | admin w/o PII permission → PII fields | fields filtered | — | BLOCKED | Pending Phase 9 |
| PRIV-008 | API export leakage | export endpoints | filter/scope/PII review pre-download | scoped+audited export | — | BLOCKED | Pending Phase 8/10 |
| PRIV-009 | Logs containing personal data/secrets | appLogs sink | grep patterns (email/phone/token/hash) after flows | zero matches | — | BLOCKED | Pending Phase 2 logging infra |
| PRIV-010 | Analytics leakage | learning_events payloads | enumerate fields vs inventory | minimization verified | — | BLOCKED | Pending Phase 8 |
| PRIV-011 | AI context leakage (cross-student) | AI gateway | session A id in session B request | DENY_403 | — | BLOCKED | Pending Phase 7 |
| PRIV-012 | AI context leakage (cross-tenant) | AI context builder | inject Tenant B IDs into Tenant A ctx | context excluded/provider never receives | — | BLOCKED | Pending Phase 7 §117 |
| PRIV-013 | AI PII minimization | AI outbound payload | name/phone/email presence scan | absent unless justified | — | BLOCKED | Pending Phase 7 |
| PRIV-014 | Backup exposure | backup artifacts | access-list + encryption check on snapshot | encrypted + least-privilege | — | BLOCKED | Pending Phase 11 infra |
| PRIV-015 | File download exposure | resources storage | direct URL guess without authz | DENY/signed-URL required | — | BLOCKED | Pending Phase 4 |
| PRIV-016 | Signed URL expiration | signed resource URL | request after TTL | 403 expired | — | BLOCKED | Pending Phase 4/11 |
| PRIV-017 | Deleted-account access | auth flows | login/refresh/me with soft-deleted user | DENY (status gate) | — | BLOCKED | Pending Phase 3 |
| PRIV-018 | Revoked consent behavior | consent records | processing after withdrawal timestamp | processing ceases (per lawful basis) | — | BLOCKED | Pending Phase 11 §3.7 |
| PRIV-019 | Data export authorization (DSR) | DSR API | unverified request → export issued | DENY until verification | — | BLOCKED | Pending Phase 11 §3.8 |
| PRIV-020 | Data deletion authorization (DSR) | DSR workflow | direct-delete attempt/bypass | workflow-only; audit entry | — | BLOCKED | Pending Phase 11 §3.8 |

**Status vocabulary (§3 requirement):** `BLOCKED` is final until infrastructure exists; no `NOT RUN` was converted to `PASS`; nothing here was fabricated.
