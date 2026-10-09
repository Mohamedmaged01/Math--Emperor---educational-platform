# PHASE 11 — Personal Data Inventory (Addendum §3)

> Timestamp: 2026-09-25T07:02:44Z UTC · Scope: **actual repository contents only** (verified by audit, not inferred). Legend: ✅ implemented · ⏳ planned (Phases 2+ blueprints) — under §3 the inventory must not claim columns that do not exist.

## Classification Scale (§3.3)
`PUBLIC` · `INTERNAL` · `CONFIDENTIAL` · `PERSONAL` · `SENSITIVE_PERSONAL`

## Inventory

| Data Element | Classification | Owner | Processing Purpose | Source | Storage Location | Database Table | API | Tenant Scope | Access Roles | Retention Period | Encryption Requirement | Exportability | Deletion Requirement | Third-Party Sharing | AI Processing | Logging Restrictions |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Theme preference (`light/dark/system`) | PUBLIC→INTERNAL | User | UI preference | localStorage `math-emperor-theme` | Browser only | — (none) | — | per-device | User self | Until cleared | N/A (non-sensitive) | N/A | User clears browser | None | None | Do not log beyond preference value |
| Demo/simulator inputs (in-page answers, scores — ephemeral) | INTERNAL | User (demo) | Interactive quiz demo | Runtime memory only | RAM | — | — | session | User self | Page lifetime | N/A | N/A | On unload | None | None | Not logged |
| Testimonial/stats content in `content.ts` | PUBLIC (marketing fiction — clearly product copy) | Platform | Landing content | Static file | Repository→bundle | — | — | public | Anonymous web | Product lifetime | Public | Public | On content update | CDN | None | Public |
| users / profiles / sessions / RBAC / audit data | PERSONAL–SENSITIVE_PERSONAL | Data subject + platform | IAM | ⏳ Phase 2–3 | ⏳ Postgres | ⏳ users, profiles, sessions… | ⏳ /api/v1 | tenant_id | Self + scoped staff | ⏳ policy TBD | At-rest + argon2id hashes | Subject-request export | DSR deletion/anonymize workflow (§3.8) | No sharing by default | Minimal context only | No passwords/tokens/hashes in logs |
| Academic/learning/assessment records | PERSONAL (student) – CONFIDENTIAL | Student | Education | ⏳ Phases 4–6 | ⏳ Postgres | ⏳ learning.*, exam_* | ⏳ | tenant + relationship | Self, linked parent, scoped teacher | ⏳ per policy | At-rest | Export with auth | Policy-driven | None default | Minimized, tenant-scoped | No answers/keys in logs |
| AI conversations/usage | PERSONAL | User | Tutoring | ⏳ Phase 7 | ⏳ Postgres (+object store per design) | ⏳ ai_* | ⏳ | tenant+owner | Self | ⏳ configurable retention | At-rest | Export w/ auth | Retention-limited | Provider per policy §3.6 | Core function | No full conversations in logs |
| Financial data (orders/payments/invoices) | SENSITIVE_PERSONAL/CONFIDENTIAL | Payer/Tenant | Billing | ⏳ Phase 10 | ⏳ Postgres | ⏳ orders, payments… | ⏳ | tenant+owner+finance | Owner + finance scope | ⏳ legal retention | At-rest + TLS | Regulated export | Never destructive (legal hold aware) | Provider only | None | Never card/CVV/credentials |
| Notifications | PERSONAL | Recipient | Comms | ⏳ Phase 10 | ⏳ Postgres | ⏳ notifications* | ⏳ | tenant+recipient | Self | ⏳ | At-rest | — | Policy | Email/SMS per preference | None | No sensitive payloads in push/log |

**Four intentional rules embedded above (§3.3):** nothing marked PERSONAL+ may flow into logs/analytics-events/AI-prompts beyond documented necessity; parent access always `Parent ↔ Student` relationship-checked (§3.5); AI context tenant+owner isolated (§3.6); DSR deletion is a verified workflow, never direct DB deletion (§3.8).
