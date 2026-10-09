# 03 — Database Blueprint (Design Only — NOT implemented in Phase 0)

مخطط قاعدة البيانات — تصميم وتوثيق فقط. Target engine: **PostgreSQL 16**.

Notation: `col:type` · `PK` primary key · `FK→` foreign key · `UQ` unique · `NN` not-null · `SD` soft-delete (`deleted_at`) · `T` carries `tenant_id`. All tables include audit fields (`created_at`, `updated_at`; `created_by`, `updated_by` on curated content) unless marked `no-audit`.

---

## 1. Domains & Tables

### 1.1 Core
```text
tenants            id:uuid PK · slug:citext UQ NN · name, display_name_ar · plan_tier · status(active|suspended)
                   domains:jsonb · settings:jsonb · SD
users              id:uuid PK · T NN FK→tenants · email:citext · phone:E164 · password_hash(algo-argon2id)
                   status(active|invited|suspended|deleted) · locale(ar) · last_login_at · SD
                   UQ(tenant_id,email) · UQ(tenant_id,phone) nullable
profiles           id:uuid PK · T FK→users NN · kind(student|parent|teacher|staff) · full_name · avatar_url
                   gender · birth_date · city · meta:jsonb · SD
roles              id:uuid PK · T FK→tenants NULL(global set) · code UQ· name_ar · is_system:bool
permissions        id:uuid PK · code UQ NN (resource.action) · module · description_ar · no-audit
user_roles         user_id FK→users · role_id FK→roles · T · PK(user_id,role_id,tenant_id)
role_permissions   role_id FK→roles · permission_id FK→permissions · PK(role_id,permission_id)
sessions           id:uuid PK · user_id FK→users · T · device:jsonb · ip:inet · refresh_hash UQ · expires_at · revoked_at
audit_logs         id:bigint PK · T · actor_id FK→users · action · entity_type · entity_id · before:jsonb · after:jsonb
                   ip · ua · created_at · no-updated_at (append-only)
```

### 1.2 Academic (KSA scope — Secondary only)
```text
countries          id:smallint PK · code UQ(SA) · name_ar · no-audit, global
education_systems  id PK · country_id FK→countries · code UQ· name_ar(الثانوية العامة/نظام المسارات) · global
grades             id PK · system_id FK→education_systems · ordinal(1|2|3) · name_ar(الأول/الثاني/الثالث الثانوي)
                   UQ(system_id,ordinal) · global
tracks             id PK · grade_id FK→grades · code(عام|علمي|صحي|إنساني…) · UQ(grade_id,code) · global
semesters          id PK · grade_id FK→grades · ordinal(1|2|3) · UQ(grade_id,ordinal) · global
subjects           id PK · T NULL(global licensed) · code · name_ar(رياضيات …) · global-or-tenant
units              id PK · subject_id FK→subjects · semester_id FK→semesters · ordinal · title_ar · UQ(subject_id,semester_id,ordinal)
lessons            id PK · unit_id FK→units · ordinal · title_ar · duration_est_min · UQ(unit_id,ordinal)
topics             id PK · lesson_id FK→lessons · ordinal · title_ar
skills             id PK · topic_id FK→topics · code UQ · name_ar · difficulty_base(1..5) · qiyas_tag(قدرات|تحصيلي) NULL
skill_dependencies skill_id FK→skills · depends_on FK→skills · PK(skill_id,depends_on) · CHECK(skill_id<>depends_on) · DAG enforced in app
```

### 1.3 LMS
```text
courses            id PK · T · subject_id FK→subjects · title_ar · slug UQ(tenant_id,slug) · kind(recorded|live|hybrid)
                   level · price: numeric NULL(free) · status(draft|published|archived) · published_at · SD
course_sections    id PK · course_id FK→courses · ordinal · title_ar · UQ(course_id,ordinal)
course_lessons     id PK · section_id FK→course_sections · lesson_id FK→lessons NULL · ordinal · type(video|article|quiz|live)
                   is_free_preview:bool
enrollments        id PK · T · course_id FK→courses · student_id FK→users · source(purchase|grant|tenant) 
                   status(active|completed|refunded) · enrolled_at · completed_at · UQ(course_id,student_id)
lesson_progress    id PK · enrollment_id FK→enrollments · course_lesson_id FK→course_lessons · status · score
                   seconds_watched · completed_at · UQ(enrollment_id,course_lesson_id)
videos             id PK · T · course_lesson_id FK→course_lessons · provider · provider_id · duration_s · hls_url · captions:jsonb
resources          id PK · T · owner(polymorphic: course|lesson|vault) · title · file_url · mime · bytes · download_count
assignments        id PK · T · course_id FK→courses · lesson_id FK→lessons NULL · spec:jsonb · due_at · max_score
```

### 1.4 Questions (Item Bank)
```text
questions          id PK · T NULL(global bank) · skill_id FK→skills · type(mcq|numeric|true_false) · stem_ar:text
                   difficulty(1..5) · source(manual|ai|import) · status(draft|in_review|approved|published|retired)
                   irt_a,irt_b,irt_c numeric NULL · version_of FK→questions NULL · current_version:int · SD
question_options   id PK · question_id FK→questions · ordinal · text_ar · is_correct:bool · UQ(question_id,ordinal)
question_tags      question_id FK→questions · tag · PK(question_id,tag)
question_skills    question_id FK→questions · skill_id FK→skills · weight numeric default 1 · PK(question_id,skill_id)
question_versions  id PK · question_id FK→questions · version:int · snapshot:jsonb · changed_by FK→users · note · UQ(question_id,version)
question_reviews   id PK · question_id FK→questions · reviewer_id FK→users · from_status · to_status · comment · created_at
```

### 1.5 Assessment
```text
exams              id PK · T · kind(qudurat_sim|tahsili_sim|skill_check|custom) · title_ar · duration_min
                   spec:jsonb (sectioning, timing) · status(draft|published|archived) · SD
exam_sections      id PK · exam_id FK→exams · ordinal · title · duration_s · question_count · UQ(exam_id,ordinal)
exam_questions     id PK · exam_section_id FK→exam_sections · question_id FK→questions · ordinal · UQ(exam_section_id,ordinal)
exam_sessions      id PK · T · exam_id FK→exams · student_id FK→users · status(created|running|submitted|expired)
                   started_at · deadline_at · submitted_at · idempotency_key UQ
exam_attempts      id PK · session_id FK→exam_sessions · attempt_no:int · UQ(session_id,attempt_no)
exam_answers       id PK · attempt_id FK→exam_attempts · exam_question_id FK→exam_questions · selected_option_id FK→question_options NULL
                   is_correct:bool NULL(unscored) · time_ms · marked_review:bool · answered_at · UQ(attempt_id,exam_question_id)
exam_results       id PK · attempt_id FK→exam_attempts UQ · score_raw · score_pct · per_skill:jsonb · predicted_qiyas:int NULL
                   completed_at (computed once — immutable result snapshot)
```

### 1.6 Learning
```text
learning_profiles  id PK · T · student_id FK→users UQ · daily_capacity_min · preferred_modalities:jsonb · pace · locale
learning_goals     id PK · student_id FK→users · target_exam(qudurat|tahsili|subject) · target_score · target_date · status
learning_paths     id PK · goal_id FK→learning_goals · generated_by(policy|ai_assisted) · spec:jsonb (ordered skill ids) · version
study_plans        id PK · T · student_id FK→users · goal_id FK→learning_goals NULL · starts_at · weeks_no · status · auto_adjust:bool
study_plan_items   id PK · plan_id FK→study_plans · day_date · ordinal · item_type(lesson|practice|simulate|review)
                   ref_type · ref_id · duration_min · status(pending|done|skipped|rescheduled) · SD
skill_mastery      id PK · student_id FK→users · skill_id FK→skills · p_mastery numeric · attempts_n · correct_n
                   last_practiced_at · half_life_days · UQ(student_id,skill_id)
recommendations    id PK · T · student_id FK→users · kind(next_lesson|review|practice|simulate) · ref_type · ref_id
                   reason_ar · rank_score · status(shown|accepted|dismissed|expired) · created_at · expires_at
```

### 1.7 AI
```text
ai_sessions        id PK · T · user_id FK→users · kind(tutor|planner|insight) · model · tokens_in · tokens_out · cost_usd
ai_messages        id PK · session_id FK→ai_sessions · role(system|user|assistant|tool) · content:text · tool_calls:jsonb
ai_requests        id PK · T · user_id FK→users · purpose · provider · model · prompt_hash UQ · latency_ms
                   status · guardrail_flags:jsonb · created_at — full payload in object storage, DB keeps hash/meta
ai_recommendations id PK · recommendation_id FK→recommendations UQ · model · features:jsonb · explanation_ar
ai_generated_questions id PK · T · source_request FK→ai_requests · question_id FK→questions NULL(once approved)
                   status(generated|validated|rejected_auto|in_review|changes_requested|approved|published) · checks:jsonb
ai_feedback        id PK · T · target(ai_message|generated_question|explanation) · ref_id · user_id FK→users · rating(-1|1..5) · comment
```

### 1.8 Analytics
```text
learning_events    event_id:uuid PK(dedup) · T NN · event_name NN · actor_id NULL · entity_type · entity_id NULL
                   metadata:jsonb · session_id · occurred_at · ingested_at · schema_version · append-only ·
                   PARTITION BY RANGE (occurred_at) monthly
performance_snapshots id PK · T · student_id FK→users · scope(skill|subject|exam_kind) · scope_id · period(day|week)
                   metrics:jsonb · taken_at · UQ(student_id,scope,scope_id,period,taken_at)
student_metrics / course_metrics / question_metrics / skill_metrics
                   id PK · T · <entity>_id FK · period · n_events · n_v2* · avg_score · avg_time_ms · difficulty observed…
                   (rollup tables fed by learning_events; UQ(entity,period))
```

### 1.9 Gamification
```text
points             id PK · T · student_id FK→users · source(event kind) · ref_id · amount:int · created_at · idempotency UQ
badges             id PK · T NULL(global) · code UQ· name_ar · icon · criteria:jsonb
achievements       id PK · badge_id FK→badges · N/A alias view over student_achievements (kept for API naming only)
student_achievements student_id FK→users · badge_id FK→badges · earned_at · PK(student_id,badge_id)
streaks            id PK · T · student_id FK→users · kind(daily_solve|plan_adherence) · current_no · best_no · last_day
challenges         id PK · T · scope(tenant|national) · kind(weekly_sprint|duel) · window(start_at,end_at) · spec:jsonb · status
challenge_attempts id PK · challenge_id FK→challenges · student_id FK→users · score · rank int NULL · UQ(challenge_id,student_id,window)
leaderboards       id PK · T · challenge_id FK→challenges NULL(rolling) · period · rank:int · student_id · score · materialized (refreshed)
```

### 1.10 Commerce
```text
plans              id PK · T NULL(global catalog) · code UQ· name_ar · price numeric · currency(SAR) · interval(month|year|once)
                   features:jsonb · status
subscriptions      id PK · T · user_id FK→users · plan_id FK→plans · status(trialing|active|past_due|canceled)
                   current_period_start/end · cancel_at_period_end:bool
orders             id PK · T · user_id FK→users · plan_id FK→plans · amount · vat_pct · total · status(pending|paid|failed|refunded)
payments           id PK · order_id FK→orders UQ· provider(moyasar|tap|hyperpay) · provider_ref UQ · method(mada|visa|applepay)
                   amount · status · raw:jsonb
invoices           id PK · order_id FK→orders UQ · number UQ(tenant_id,number) · zatca_fields:jsonb · pdf_url · issued_at
coupons            id PK · T · code UQ(tenant_id,code) · kind(pct|fixed) · value · max_uses · used_n · valid_from/to · status
```

---

## 2. §0.15 — Database Rules

| Rule | Decision |
|---|---|
| Primary Keys | `uuid` (v7, time-ordered) for entity tables; `bigint identity` for append-only logs (`audit_logs`); composite natural PKs for join tables. |
| Foreign Keys | Mandatory, named `fk_<table>_<col>`; `ON DELETE RESTRICT` default; `CASCADE` only for true composition (options, sections, items); soft-deleted parents keep children discoverable. |
| Unique Constraints | Business uniques always composite with `tenant_id` on tenant tables (see `UQ(tenant_id,email)`, slugs, coupon codes, invoice numbers). |
| Indexes | Every `tenant_id`; every FK column; `status` on workflow tables; `occurred_at` on events; `skill_id` on questions/mastery. |
| Composite Indexes (leading tenant) | `questions(tenant_id,status,skill_id)` · `exam_sessions(tenant_id,student_id,status)` · `learning_events(tenant_id,actor_id,occurred_at)` · `study_plan_items(plan_id,day_date)` · `skill_mastery(student_id,skill_id)` (PK) · `recommendations(tenant_id,student_id,status,rank_score DESC)`. |
| Soft Deletes | Enabled on: tenants, users, profiles, courses, questions, exams, study_plan_items. Never on financial or event/result tables (immutable). |
| Audit Fields | `created_at/updated_at` everywhere; `created_by/updated_by` on curated content (academic, questions, exams, cms); append-only tables get `created_at` only. |
| Tenant Isolation | PostgreSQL **RLS**: `USING (tenant_id = current_setting('app.tenant_id')::uuid)` on every T-table; platform roles connect with bypass role. Migrations add RLS in the same PR as the table — no exceptions. |
| Referential Integrity | Mastery↔skill↔question chain restrict-delete; results immutable snapshots; payments immutable post-settlement (corrections via refund rows). |
| Scale Plan (millions of rows) | `learning_events` monthly partitions + cold archive; `exam_answers` partitioned by attempt month (Phase 2); read replicas for analytics rollups; rollups computed off primary; hot data only in Redis for leaderboards (sorted sets) with periodic materialization to `leaderboards`. |
| Concurrency/Integrity extras | Idempotency keys on `exam_sessions`, `payments`, `points`; `CHECK` constraints for enums mirrored in DB enums; optimistic locking `version` on curated content; advisory locks for rollup jobs. |
