# QA — Operational Health Matrix

Legend: `PASS` = end-to-end verified (persistence + audit evidence) · `D-FAIL` = DEMO-FAIL per §4 · `N/I` = NOT IMPLEMENTED · `BLK` = BLOCKED · `N/A` = NOT APPLICABLE.

| Module | Entity | Create | Read | Search | Update | Delete | Upload | Import | Export | Permission | DB | Audit | Perf | Final |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Landing | static sections | N/A | PASS | N/A | N/A | N/A | N/A | N/A | N/A | N/A | N/A | N/A | PASS (serve 200) | PASS |
| Theme | preference | local | local | N/A | local | N/A | N/A | N/A | N/A | self | FAIL(§4) | N/A | N/A | D-FAIL |
| Auth | accounts/sessions | local | local | N/A | local | local | N/A | N/A | N/A | FAIL(§4) | FAIL(§4) | local | N/A | D-FAIL |
| Simulator | attempts | PASS (memory) | PASS | N/A | PASS | N/A | N/A | N/A | N/A | N/A | N/A | N/A | PASS | PASS (client) |
| Advisor | Q&A | N/A | PASS | N/A | N/A | N/A | N/A | N/A | N/A | N/A | N/A | N/A | PASS | PASS (client) |
| Portal | sim records | local | local | N/A | N/A | N/A | N/A | N/A | N/A | FAIL(§4) | FAIL(§4) | N/A | N/A | D-FAIL |
| Admin | all store entities | local | local | local | local | local | N/A | local | PASS (real files) | FAIL(§4) | FAIL(§4) | local | N/A | D-FAIL |
| Audit | entries | local | local | N/A | N/A | N/A | N/A | N/A | PASS (real files) | FAIL(§4) | FAIL(§4) | — | N/A | D-FAIL (not server audit) |
| CMS→Hero | override string | N/A | PASS | N/A | local | N/A | N/A | N/A | N/A | FAIL(§4) | FAIL(§4) | local | N/A | D-FAIL |
| Users/Students/Teachers (server) | profiles | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/A | N/I | N/I | **BLOCKED — NOT IMPLEMENTED** |
| Academic/LMS | curriculum/courses | N/I … | → → → full row N/I | | | | | | | | | | **BLOCKED — NOT IMPLEMENTED** |
| Questions (server) | items/versions | N/I … | full row N/I | | | | | | | | | | **BLOCKED — NOT IMPLEMENTED** |
| Assessment (server) | exams/attempts/results | N/I … | | | | | | | | | | | **BLOCKED — NOT IMPLEMENTED** |
| Learning/AI/Analytics | engines | N/I … | | | | | | | | | | | **BLOCKED — NOT IMPLEMENTED** |
| Commerce | plans/payments | N/I … | | | | | | | | | | | **BLOCKED — NOT IMPLEMENTED** |
| Storage/Files | uploads/signed URLs | N/I … | | | | | | | | | | | **BLOCKED — NOT IMPLEMENTED** |
| Tenant layer | isolation | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/A | N/I | N/I | **BLOCKED — NOT IMPLEMENTED** |
| Test suite | automated tests | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | N/I | **BLOCKED — no runner** |

## Scorecard (no numeric fake-score; categorical)

- **Verifiable PASS surfaces:** presentation serve (build+HTTP 200), Simulator game logic, Advisor logic, client file-export artifact generation, demo guard-denial auditing, dynamic-sidebar role wiring.
- **Every server-authoritative dimension (API / DB / Auth-server / RBAC-server / Tenant / Storage / Server audit / Query perf / Concurrency / Backups / Automated tests): BLOCKED or NOT IMPLEMENTED.**
- **Every localStorage-backed CRUD flow: §4 FAIL for this validation standard** (functional demo ≠ validated database CRUD).
