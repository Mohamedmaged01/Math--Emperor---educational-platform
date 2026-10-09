# 04 — API Blueprint, Response Standard & RBAC (Contract Only — NOT implemented in Phase 0)

العقد البرمجي والصلاحيات — تصميم فقط. Version prefix: **`/api/v1`**. Transport: HTTPS+JSON. Auth: `Authorization: Bearer <access_token>` (short-lived) + rotating refresh cookie (`HttpOnly; Secure; SameSite=Lax`). Tenant resolution: subdomain/domain header `X-Tenant` (validated server-side against token claim).

---

## 1. §0.17 — API Response Standard (adopted verbatim)

**Success**
```json
{ "success": true, "data": {}, "message": null, "meta": {} }
```
**Error**
```json
{ "success": false, "data": null, "message": "Validation failed", "errors": { "email": ["INVALID_FORMAT"] }, "meta": {} }
```
**meta** carries `request_id`, `trace_id`, pagination (`page`, `per_page`, `total`, `has_next`), `tenant_id`(echo), deprecation notices.

| HTTP | code (in `errors`/`message`) | Meaning |
|---|---|---|
| 400 | VALIDATION_FAILED | schema violation; `errors` = field map |
| 401 | UNAUTHENTICATED / TOKEN_EXPIRED | login/refresh |
| 403 | FORBIDDEN (RBAC) · TENANT_SCOPE_VIOLATION | permission or cross-tenant |
| 404 | NOT_FOUND | hidden vs forbidden: 404 for out-of-tenant too |
| 409 | CONFLICT / IDEMPOTENCY_REPLAY | unique violation, replayed mutation |
| 422 | BUSINESS_RULE_VIOLATION | domain rule (e.g. exam already submitted) |
| 429 | RATE_LIMITED | per-user & per-tenant buckets |
| 5xx | INTERNAL · UPSTREAM_ERROR | never leak internals; `trace_id` only |

**Naming:** kebab-case paths, plural nouns, verbs only via sub-action (`/attempts/{id}:submit`). All list endpoints: `?page&per_page≤100&sort&filter[...]`. Idempotency: header `Idempotency-Key` mandatory on POST mutations that charge/submit.

---

## 2. §0.16 — Endpoint Groups (representative contracts)

| Group | Key endpoints (method + path) | Permission (see §3) |
|---|---|---|
| `/auth` | POST `/auth/register` · `/auth/login` · `/auth/refresh` · `/auth/logout` · `/auth/forgot-password` · POST `/auth/otp/verify` | public |
| `/users` | GET/PATCH `/users/me` · GET `/users/{id}` | profile.self.read · user.read |
| `/students` | GET/PATCH `/students/me/profile` · GET `/students/{id}/mastery` · GET `/students/{id}/overview` (parent/teacher link) | student.self.* · student.read |
| `/parents` | POST `/parents/link-student` · GET `/parents/me/children/{id}/report` | parent.child.read |
| `/teachers` | GET `/teachers/me/cohorts` · GET `/teachers/me/cohorts/{id}/progress` | cohort.read |
| `/academic` | GET `/academic/grades|tracks|semesters|subjects|units|lessons|topics|skills` (tree filters) · POST/PUT same (admin) | academic.read · academic.manage |
| `/courses` | GET list/detail · POST create · POST `/courses/{id}/sections` · POST `/courses/{id}:publish` | course.read/create/publish |
| `/lessons` | GET `/lessons/{id}` · POST `/lesson-progress` (heartbeat/complete) | lesson.read · progress.write|own |
| `/questions` | GET `/questions` (filters) · POST create · PATCH `/{id}` · POST `/{id}:submit-review` · `/{id}:approve` · `/{id}:publish` · GET `/questions/{id}/versions` | question.read/create/update/review/publish |
| `/exams` | GET catalog/detail · POST CRUD · POST `/exams/{id}:publish` | exam.read/manage/publish |
| `/attempts` | POST `/exams/{id}/sessions` (start; idempotent) · POST `/attempts/{id}/answers:batch` · POST `/attempts/{id}:submit` · GET `/attempts/{id}/result` | attempt.own · result.read|own |
| `/skills` | GET `/skills/{id}/mastery?student=` · GET `/skills/tree` | skill.read · mastery.read |
| `/learning` | POST `/learning/goals` · POST `/learning/plans:generate` · GET `/learning/plans/current` · PATCH `/plan-items/{id}` (done/skip) · GET `/learning/recommendations` · POST `/recommendations/{id}:accept|:dismiss` | learning.self.* · learning.manage (admin) |
| `/analytics` | POST `/analytics/events:batch` (ingest) · GET `/analytics/dashboards/student|course|question` | event.write|own · analytics.read |
| `/ai` | POST `/ai/tutor/messages` (SSE) · POST `/ai/feedback` · GET `/ai/generated-questions?status=…` · POST `/ai/generated-questions/{id}:approve|:reject` | ai.use · ai.review · ai.approve |
| `/challenges` | GET current · POST `/{id}/attempts` · GET `/{id}/leaderboard` | challenge.join · leaderboard.read |
| `/live` | GET `/live/classes` · POST `/live/classes/{id}:join` (signed token) · GET recordings | live.read/join |
| `/resources` | GET `/resources` (vault, signed URLs) · POST upload (admin) · POST `/resources/{id}:track-download` | resource.download · resource.manage |
| `/subscriptions` | GET `/plans` · POST `/subscriptions` (checkout session) · POST `/{id}:cancel` | billing.own |
| `/payments` | POST `/payments/webhooks/{provider}` (signature-verified, provider-only) · GET `/payments/{id}` | webhook internal · billing.read|own |
| `/notifications` | GET `/notifications/me` · POST `/{id}:read` · admin broadcast POST `/notifications/broadcast` | notification.own · notification.broadcast |
| `/support` | POST `/support/tickets` · GET mine · agent: PATCH `/{id}` | support.own · support.manage |
| `/cms` | GET `/cms/landing|pricing|faqs|testimonials` (public, cached) · admin CRUD | cms.read(public) · cms.manage |
| `/admin` | GET `/admin/overview` · `/admin/users` (search/guard) · `/admin/tenants` (super) · `/admin/audit-logs` | admin.dashboard · user.manage · tenant.manage · audit.read |

Versioning: breaking changes → `/api/v2` alongside; sunset headers ≥ 90 days. OpenAPI per module is CI-validated (Phase 1 tooling).

---

## 3. §0.18 — RBAC (permission-based, real)

**Format:** `resource.action[.scope]` — e.g. `question.publish`, `attempt.own`, `student.read:any`.

### 3.1 Roles (canonical set)
```text
Super Admin · Admin · Academic Manager · Content Manager · Question Manager
Teacher · Teaching Assistant · Student · Parent · Finance Manager
Support Agent · Analyst
```

### 3.2 Permission catalog (core slice)
```text
tenant.manage  admin.dashboard  audit.read  user.read/manage  role.assign
academic.read/manage   course.read/create/update/publish   lesson.read
question.read/create/update/delete/review/publish
exam.read/manage/publish   attempt.own   result.read:own/:any
skill.read   mastery.read   learning.self.*   learning.manage
analytics.read:own/:tenant   event.write:own
ai.use  ai.review  ai.approve  ai.feedback
challenge.join  leaderboard.read   live.read/join/manage
resource.download/manage   subscription.own/manage   payment.read:own/refund
notification.own/broadcast   support.own/manage   cms.read/manage
```

### 3.3 Role → permission matrix (summary)
| Permission group | Student | Parent | Teacher | TA | Question Mgr | Content Mgr | Academic Mgr | Finance | Support | Analyst | Admin | Super Admin |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| attempt.own / learning.self / ai.use | ✓ | – | – | – | – | – | – | – | – | – | – | – |
| student.read / mastery.read (linked/cohort) | own | linked | cohort | cohort | – | – | ✓ | – | ✓(limited) | ✓ | ✓ | ✓ |
| question.create/update | – | – | suggest | suggest | ✓ | – | ✓ | – | – | – | ✓ | ✓ |
| question.review | – | – | – | – | ✓ | – | ✓ | – | – | – | ✓ | ✓ |
| question.publish | – | – | – | – | ✓ | – | ✓ | – | – | – | ✓ | ✓ |
| ai.review / ai.approve | – | – | – | – | ✓ review | – | ✓ approve | – | – | – | ✓ | ✓ |
| course.publish / academic.manage | – | – | – | – | – | publish(cms/course copy) | ✓ | – | – | – | ✓ | ✓ |
| exam.manage/publish | – | – | custom | – | – | – | ✓ | – | – | – | ✓ | ✓ |
| payment.refund / finance.read | – | – | – | – | – | – | – | ✓ | read-only | – | ✓ | ✓ |
| support.manage | – | – | – | – | – | – | – | – | ✓ | – | ✓ | ✓ |
| analytics.read:tenant | – | – | – | – | – | – | ✓ | – | – | ✓ | ✓ | ✓ |
| tenant.manage / role.assign / audit.read | – | – | – | – | – | – | – | – | – | – | partial | ✓ |
| notification.broadcast / cms.manage | – | – | – | – | – | cms ✓ | – | – | broadcast(support macros) | – | ✓ | ✓ |

### 3.4 Enforcement model
```text
Request → AuthN → TenantContext → RBAC Guard (permission check)
        → Ownership/Scope check (own|linked|cohort|any) → Application Service
```
1. **Server-side only.** UI hides, server denies (403 with `FORBIDDEN`).
2. **Permissions, not roles, in code.** ❌ BANNED: `if (role === 'admin')`. ✅ `authorize('question.publish', ctx)`.
3. Role→permission grants live in DB (`role_permissions`) and are hot-cached per tenant.
4. Scope resolution: `own` = actor is subject; `linked` = parent/guardian edge; `cohort` = teacher assignment; `any` = staff within tenant; platform-wide only via elevated Super Admin context (audited).
5. Every denied check on sensitive resources writes `audit_logs` entry.
6. Default-deny: no grant ⇒ no access.

---

## 4. Security baseline (deploy-layer, scheduled Phase 1 — documented now)
CSP + HSTS + X-Frame-Options/Frame-Ancestors + Referrer-Policy at edge · argon2id hashing · refresh rotation + theft detection · provider webhook signature verification · PII minimization in events · secrets via manager (never repo) · rate limits on auth & AI endpoints · full audit trail for admin/finance actions (PDPL alignment for KSA data residency where required).
