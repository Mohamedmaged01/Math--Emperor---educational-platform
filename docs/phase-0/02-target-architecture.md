# 02 — Target Architecture

البنية المستهدفة: Modular Monolith + API First · Multi-Tenant · Adaptive Learning + Governed AI + Learning Analytics.

---

## 1. §0.6 — Target Layered Architecture

```text
┌────────────────────────────────────────────────────────────────┐
│ PRESENTATION LAYER                                             │
│ Web SPA (React/TS · features/*) · Student Portal · Admin Panel │
└──────────────────────────────┬─────────────────────────────────┘
                               ↓  HTTPS · JSON · /api/v1
┌──────────────────────────────┴─────────────────────────────────┐
│ API LAYER                                                      │
│ REST /api/v1 · AuthN (JWT/OIDC) · Tenant Resolver · RBAC Guard │
│ Validation · Rate Limiting · Response Standard · OpenAPI       │
└──────────────────────────────┬─────────────────────────────────┘
                               ↓
┌──────────────────────────────┴─────────────────────────────────┐
│ APPLICATION LAYER (Use-Cases / Services)                       │
│ e.g. StartExamSession · SubmitAnswer · GenerateStudyPlan ·     │
│ EnrollCourse · IssueRecommendation · ApproveAIGeneratedItem    │
└──────────────────────────────┬─────────────────────────────────┘
                               ↓
┌──────────────────────────────┴─────────────────────────────────┐
│ DOMAIN MODULES (Modular Monolith — see §2)                     │
│ Identity · RBAC · Tenant · Academic · LMS · Questions ·        │
│ Assessment · Learning · AI · Analytics · Gamification ·        │
│ Live Learning · Resources · Notifications · Commerce · CMS ·   │
│ Support                                                        │
└──────────────────────────────┬─────────────────────────────────┘
                               ↓
┌──────────────────────────────┴─────────────────────────────────┐
│ INFRASTRUCTURE                                                 │
│ ORM/Repositories · Event Bus (outbox) · Queues · Storage SDK · │
│ LLM Provider Clients · Payment Gateways · Notification Senders │
└──────────────────────────────┬─────────────────────────────────┘
                               ↓
┌──────────────────────────────┴─────────────────────────────────┐
│ PostgreSQL (RLS) · Redis (cache/queues) · Object Storage ·     │
│ Search (OpenSearch) · Analytics sink (columnar, later)         │
└────────────────────────────────────────────────────────────────┘
```

**Dependency rule:** outer layers depend inward only. Domain modules never import from Presentation or API; they depend on Infrastructure abstractions. Cross-module calls go through each module's public Application service (never direct table access).

---

## 2. Domain Modules (Modular Monolith)

| Module | Responsibility | Owns (tables, see Doc 03) | Future extractable? |
|---|---|---|---|
| Identity | Users, profiles, sessions, credential verification | users, profiles, sessions | Yes → auth service |
| RBAC | Roles, permissions, grants, policy checks | roles, permissions, user_roles, role_permissions | Bundled with Identity |
| Tenant | Tenant lifecycle, domains, tenant settings | tenants | Foundational (stays) |
| Academic | KSA taxonomy: grades→tracks→semesters→subjects→units→lessons→topics→skills | academic.* | Yes → content-graph service |
| LMS | Courses, structure, enrollment, progress, media | courses…, enrollments, lesson_progress, videos, resources, assignments | Yes |
| Questions | Item bank, options, tags, skills, versioning, review workflow | questions… | Yes → item-bank service |
| Assessment | Exams, sections, sessions, attempts, answers, results | exams… | Yes → assessment engine |
| Learning | Profiles, goals, plans, paths, mastery, recommendations | learning.* | Yes → adaptive engine |
| AI | Tutor, generators, insights; orchestration + governance status | ai_* | Yes → AI service first |
| Analytics | Event ingest, snapshots, metrics | learning_events, *_metrics, performance_snapshots | Yes → analytics pipeline |
| Gamification | Points, badges, streaks, challenges, leaderboards | points, badges… , challenges… | Later |
| Live Learning | Live classes, attendance, recordings | (live_classes, live_sessions… — Phase 2+) | Later |
| Resources | Downloadable vault files | resources (+ vaults later) | Later |
| Notifications | Email/SMS/WhatsApp/push orchestration | notifications, notification_templates | Yes |
| Commerce | Plans, subscriptions, orders, payments, invoices, coupons | plans…, payments… | Yes → billing service |
| CMS | Landing content, testimonials, FAQs, pricing display | cms_* (later) | Later |
| Support | Tickets, conversations | support_tickets (later) | Later |

**Extraction rule:** a module gets split out only with a documented driver (scale, team ownership, compliance). Until then: one deployable, strict internal boundaries.

## 3. §0.7 — Architectural Style: MODULAR MONOLITH + API FIRST

- **Why (recorded):** team size/early product ⇒ deployment & transactional simplicity; web + future mobile ⇒ contract-first API; module boundaries designed so Services can be split later without re-modeling.
- Contracts (OpenAPI per module) are source of truth; frontend and backend both generate/consume from them.

---

## 4. §0.8 — SaaS Model (Multi-Tenant from Day 1)

**Entity:** `Tenant` (a school/institute/brand; the public Math Emperor brand itself is tenant id `default`).

| Concern | Design |
|---|---|
| Isolation strategy | **Shared DB, shared schema, `tenant_id` discriminant** + PostgreSQL **Row-Level Security** on every operational table. (Dedicated-schema option reserved for enterprise tier.) |
| Tenant context | Resolved at API edge (subdomain/custom domain → `tenant_id`), placed in immutable request-scoped `TenantContext`; injected into every repository call automatically. |
| Authorization | RBAC permissions are evaluated **within tenant scope**; cross-tenant access only for platform roles (`Super Admin`) via explicit elevated context. |
| Tenant-aware queries | All repositories enforce `WHERE tenant_id = :ctx`; composite indexes lead with `tenant_id` (Doc 03 §2). |
| Tenant-aware caching | Cache keys namespaced `t:{tenant_id}:{domain}:{key}`; invalidation events carry `tenant_id`. |
| Tenant-aware events | Every analytics/domain event includes `tenant_id` (see §8 envelope). |
| Global tables | Reference data (countries, education_systems, and licensed public item-bank subsets) are tenant-nullable/global-readable; overrides per tenant. |

---

## 5. §0.9 — Academic Model (KSA scope only)

```text
Country (Saudi Arabia)
 └─ Education System (General Secondary / مقررات+مسارات)
     └─ Grade (First / Second / Third Secondary — الصف الأول/الثاني/الثالث الثانوي)
         └─ Track (عام · علمي · صحي · إنساني …)
             └─ Semester (ف1 · ف2 · ف3)
                 └─ Subject (رياضيات …)
                     └─ Unit
                         └─ Lesson
                             └─ Topic
                                 └─ Skill
                                     └─ Sub-Skill
```

- **Scope lock:** Secondary only. **Middle school is explicitly excluded.**
- `skill_dependencies` forms a DAG (prerequisites) consumed by the adaptive engine.
- Qudurat/Tahsili competency mappings hang off `skills` (qiyas domain tags), keeping the school curriculum and Qiyas tracks linkable but distinct.

## 6. §0.10 — Learning Model

```text
User
 ↓ 1:1 (student)
Student Profile          — demographics, grade, school, guardian link
 ↓
Academic Profile         — current grade/track/semester, target exam, target score
 ↓
Learning Profile         — pace, preferred modalities, daily capacity, timezone, strengths/weaknesses summary
 ↓
Skill Mastery            — per-skill p(mastery), attempts, last_practiced, forgetting-curve state
 ↓
Personalized Learning
 ├─ Learning Goal        — target score/date (e.g. Qudurat ≥ 95 by 1447-06)
 ├─ Learning Path        — ordered skill sequence per goal (DAG-aware)
 ├─ Study Plan + Items   — scheduled tasks generated from path + capacity
 └─ Recommendation       — next-best-action (lesson/question/review/simulation)
```

Mastery updates via Bayesian/IRT-lite scoring from `exam_answers` + practice events; recommendations = policy over (mastery gaps × goal urgency × recent errors).

---

## 7. §0.11 — AI Architecture (logically independent engine)

```text
                 ┌────────────── AI ENGINE ───────────────┐
Frontend ──► AI Application Facade (tenant + RBAC + quota) │
                 │                                         │
   ┌─────────────┼───────────────┬───────────────┐        │
   ▼             ▼               ▼               ▼         │
 AI Tutor   Recommendation   Error Analysis  Study Planner│
   │             │               │               │         │
   ├─ Question Generator  ├─ Explanation Generator          │
   └─ Academic Insights   └────────────────────────         │
                 │                                         │
   Context Builder (skills/mastery/history via Application │
   services — NEVER raw DB) · Prompt/Template registry ·    │
   Provider Abstraction (LLM A/B, failover, cost metering)  │
                 │                                         │
   Guardrails: Arabic academic style guide · math-verifier  │
   · PII redaction · toxicity/scope filters                 │
                 └─────────────────────────────────────────┘
```

- **AI never touches the database directly.** All reads/writes flow through Application-layer services (same tenant/RBAC rules as any user action).
- Every request/response persisted (`ai_requests`, `ai_sessions/messages`) for audit, cost, and quality sampling.

## 8. §0.12 — AI Governance (publish pipeline for educational content)

```text
AI Generate (status: generated)
   ↓ automated checks: schema·difficulty·answer-verification·style
Validation (status: validated | rejected_auto)
   ↓ human review queue
Review (Question Manager / Teacher) (status: in_review | changes_requested)
   ↓
Academic Approval (Academic Manager) (status: approved)
   ↓
Publish (status: published → enters bank/topics)
```

**Rule:** AI has **no publish permission** for learner-facing educational content. `ai_generated_questions.status` + `question_reviews` enforce the chain; every transition writes an audit entry.

## 9. §0.13 — Analytics Architecture (Event Model)

**Envelope (all events):**
```text
event_name      e.g. QUESTION_ANSWERED
event_id        uuid (dedup)
actor_id        user id (nullable for anonymous)
tenant_id       required
entity_type     e.g. "question" | "exam" | "course" | null
entity_id       uuid | null
metadata        JSON (answer correctness, duration_ms, source, AB variant…)
occurred_at     timestamptz (client) + ingested_at (server)
session_id      client session
schema_version  int
```

**Initial catalog (non-exhaustive, includes mandated list):**
`USER_LOGIN · COURSE_STARTED · LESSON_STARTED · LESSON_COMPLETED · VIDEO_STARTED · VIDEO_COMPLETED · QUESTION_VIEWED · QUESTION_ANSWERED · QUESTION_CORRECT · QUESTION_WRONG · EXAM_STARTED · EXAM_COMPLETED · AI_REQUEST · AI_RESPONSE · CHALLENGE_COMPLETED · RESOURCE_DOWNLOADED` (+ `STUDY_PLAN_GENERATED · RECOMMENDATION_ACCEPTED · SUBSCRIPTION_STARTED · PAYMENT_SUCCEEDED`).

**Pipeline:** edge collector (batched, at-least-once) → `learning_events` (hot, partitioned) → rollups (`student_metrics`, `course_metrics`, `question_metrics`, `skill_metrics`, `performance_snapshots`) → dashboards. Learning-engine mastery consumes events; the same stream feeds Qiyas-style score prediction.

---

## 10. §0.19 — Frontend Architecture (target folder structure)

```text
src/
  app/                    # entry, providers, router (Phase 1)
  features/
    landing/              # ← today's page becomes this feature
    assessment/           # QiyasSimulator (protégé: current Simulator.tsx)
    question-bank/  courses/  live/  learning/  analytics/
    gamification/ auth/ portal/ admin/ commerce/ ai-tutor/
  components/             # shared UI primitives (Button, Card, Modal…)
    layout/               # Navbar, Footer (promoted from components/)
  services/               # cross-feature app services (session, tenant)
  api/                    # generated clients + typed endpoints per domain
  hooks/                  # useInView-counter, useCountdown, useMediaQuery…
  stores/                 # client state slices (zustand) — only what must be global
  types/                  # generated API types + domain DTOs
  utils/   constants/   styles/
```

**Separation rules (enforced in Phase 1+):**
1. `components/*` are **pure UI** — no API, no business rules.
2. Business logic lives in feature `*.service.ts`/`*.model.ts` — UI calls it via hooks.
3. Network only inside `api/` — components never call fetch.
4. Client `stores/` only for cross-feature UI/session state; server data via query cache (TanStack Query).
5. Simulator must be decomposed behind `AssessmentSession` service with its current behaviors locked by regression tests (Golden Rule) before any move.

**Mapping of existing code (no moves executed in Phase 0):** `App.tsx → app/router`, landing sections → `features/landing`, `Simulator.tsx → features/assessment`, `Plans.tsx → features/learning`, `Pricing.tsx → features/commerce`, `Navbar/Footer → components/layout`, `content.ts → features/landing/content (then CMS)`, `index.css → styles/`.
