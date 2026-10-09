# QA — Operation Matrix

Status vocabulary: `PASS` (verified end-to-end incl. persistence evidence) · `DEMO-FAIL (§4)` (functional client flow that cannot satisfy DB/API validation per rule §4) · `BLOCKED` (prerequisite missing) · `NOT IMPLEMENTED` | Evidence pointer after each group.

| ID | Module | Entity | Operation | UI | API | DB | Permission | Test | Status | Evidence |
|---|---|---|---|---|---|---|---|---|---|---|
| OP-001 | Landing | Sections/anchors | READ | ✅ | — | — | public | build+serve 200s | **PASS (presentation)** | LRS-1 |
| OP-010 | Theme | preference | READ/UPDATE | ✅ | — | localStorage | self | src grep + code path | **DEMO-FAIL (§4)** (client-only) | LS-1 |
| OP-020 | Auth | users (demo accts) | LOGIN/CHECK | ✅ | ✕ | localStorage | uniform-error guard | src grep | **DEMO-FAIL (§4)** — no server auth | LS-2 |
| OP-021 | Auth | sessions | CREATE/READ/REVOKE | ✅ | ✕ | localStorage | owner-only demo | src grep | **DEMO-FAIL (§4)** | LS-2 |
| OP-030 | Simulator | exam session | START/ANSWER/TIMEOUT/SUBMIT/SCORE | ✅ | — | in-memory | n/a | code review of frozen state machine | **PASS (client-functional; no DB claim)** | SIM-1 |
| OP-031 | Simulator→Portal | attempt record | CREATE (real record points/streak) | ✅ | ✕ | localStorage | owner demo | event + key path | **DEMO-FAIL (§4)** | LS-3 |
| OP-032 | Simulator→Portal | records | READ | ✅ | ✕ | localStorage | owner demo | key path | **DEMO-FAIL (§4)** | LS-3 |
| OP-040 | Advisor | answers/challenge | ASK/EVALUATE | ✅ | — | in-memory + content data | n/a | code review (deterministic branches) | **PASS (client-functional)** | ADV-1 |
| OP-050 | Admin store | students/teachers/questions/exams/contents/resources/orders/notifications/security/ai/approvals/flags | CREATE/READ/UPDATE/DELETE/ARCHIVE/PUBLISH/BULK | ✅ | ✕ | localStorage | demo matrix (super vs teacher) | src grep + API layer code | **DEMO-FAIL (§4)** — every CRUD op | LS-4 |
| OP-051 | Admin store | same entities | SEARCH/TARGET/AIOC CHIPS | ✅ | — | client-side filter | demo matrix | code review | **DEMO-FAIL (§4)** (no server-side search/index) | LS-4 |
| OP-052 | Admin store | import | CSV upload→parse→preview→confirm (real file read) | ✅ | ✕ | localStorage | demo matrix | code + file path exists | **DEMO-FAIL (§4)** | LS-4 |
| OP-053 | Admin store | export | real CSV/JSON file download | ✅ | — | browser Blob | demo matrix | code + blob path | **PASS (artifact generation is genuinely real; persistence n/a)** | LS-4 |
| OP-054 | Admin store | approvals | APPROVE/REJECT + reason | ✅ | ✕ | localStorage | demo matrix | code | **DEMO-FAIL (§4)** | LS-4 |
| OP-055 | Admin store | audit | WRITE/READ (real stream incl. denials) | ✅ | ✕ | localStorage | demo matrix | code + grep | **DEMO-FAIL (§4)** (not a server audit) | LS-4 |
| OP-056 | Admin store | dynamic sidebar | permission-filtered nav per role | ✅ | — | client | session role | matrix code in store.ts | **PASS (client-functional; proves matrix wiring)** | LS-4 |
| OP-057 | Admin store | CMS publish | hero title override going live | ✅ | ✕ | localStorage | demo matrix | code path → Hero reads override | **DEMO-FAIL (§4)** | LS-5 |
| OP-058 | Admin store | denied actions | denial → audit entry | ✅ | ✕ | localStorage | — | `denied()` path | **PASS (provable guard behavior; demo)** | LS-4 |
| OP-060 | Tenant | tenants, isolation | ALL | — | ✕ | ✕ | — | n/a | **NOT IMPLEMENTED** | — |
| OP-070 | RBAC (server) | roles/permissions | ENFORCE | — | ✕ | ✕ | ✕ | n/a | **NOT IMPLEMENTED** (only demo matrix) | — |
| OP-080..OP-120 | Phase-2 blueprint: grades/tracks/semesters/subjects/units/lessons/topics/skills/courses/enrollments/progress/videos/uploads(storage)/question versions/reviews/exam sessions/answers/results/learning profiles/goals/plans paths/mastery/recommendations/AI sessions/requests/analytics events/gamification/subscriptions/payments/invoices/coupons/tickets/tenants/settings/media | ALL CRUD/search/upload/import/export/relations/transactions | — | ✕ | ✕ | ✕ | n/a | **NOT IMPLEMENTED** | Phase-0 audit |
| OP-130 | Files/Storage | PDFs/ppt/xlsx/video upload→storage→signed URL | — | ✕ | ✕ | ✕ | n/a | **NOT IMPLEMENTED** (only demo resource rows) | — |
| OP-140 | Performance | server query plans / EXPLAIN | — | ✕ | ✕ | — | n/a | **BLOCKED (no DB)** | — |
| OP-141 | Concurrency | row locks/lost-update/idempotency server-side | — | ✕ | ✕ | — | n/a | **BLOCKED (no backend)** | — |
| OP-142 | Backups | backup/restore | — | — | ✕ | — | n/a | **BLOCKED (no infra)** | — |
| OP-143 | Automated suite | vitest/playwright tests | — | — | — | — | n/a | **BLOCKED (no test runner configured)** | pkg.json |

## Evidence keys
- **LRS-1**: build ok + `vite preview` 200 responses for `/`, hero jpg, course jpg (2026-09-25).
- **LS-1..LS-5**: grep inventory of the 16 browser-persistence calls (theme, session, portal, admin store, hero CMS) — see `system-inventory.md §1`.
- **SIM-1**: `src/components/Simulator.tsx` (frozen state machine incl. timeout semantics).
- **ADV-1**: `src/advisor/Advisor.tsx` (deterministic `respond()` engine).
