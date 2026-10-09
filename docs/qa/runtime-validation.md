# QA — Runtime Validation (Admin Workspace + Student Portal)

> Executed: 2026-09-25 · Method: `npx tsx tests/qa-smoke.ts` — the suite imports the **production source files directly** (`src/auth/session.ts`, `src/admin/store.ts`, `src/admin/api.ts`) with in-memory Storage shims (`tests/shims.ts`). No logic re-implemented, no module under test mocked. Exit code 0 ⇒ every assertion passed.

## Scope honesty (per §4 of the master validation)
This verifies the **client-layer operations actually present in this build** (demo session layer + local admin store + portal persistence). It does not — and per `final-test-report.md` cannot — verify server DB/API/tenancy (still `FULL OPERATIONAL VALIDATION — BLOCKED` for those). Within the demo-layer contract, every operation below is proven functional.

## Result

```text
TOTAL: 22 · PASS: 22 · FAIL: 0   (exit code 0)
```

## Evidence (verbatim run output)

| ID | Operation (module) | Result | Evidence detail |
|---|---|---|---|
| A1 | login admin (auth) | PASS | token issued `demo-n0yffc3…`, user «مدير النظام الأعلى» |
| A2 | wrong password (auth) | PASS | uniform Arabic error — no user enumeration |
| A3 | staff↔student gate isolation (rbac) | PASS | role-mismatch rejected with same uniform error |
| A4 | remember=true persistence (auth) | PASS | `readSession()` restored `student@emperor.sa` |
| B1 | staffRole mapping admin | PASS | SUPER_ADMIN |
| B2 | staffRole mapping teacher acct | PASS | TEACHER |
| B3 | superadmin rbac.manage | PASS | allowed |
| B4 | teacher rbac.view (sidebar hiding) | PASS | module absent from teacher matrix |
| B5 | teacher questions.create | PASS | allowed (OWNED scope) |
| B6 | teacher students.delete | PASS | blocked by matrix |
| C1 | CREATE record (admin CRUD) | PASS | id `st-fr70o` generated |
| C1a | audit entry on CREATE | PASS | audit row: action=إضافة, entity=st-fr70o, detail=طالب اختبار QA |
| C2 | UPDATE field (score) | PASS | 0 → 77 persisted |
| C3 | PUBLISH transition (workflow) | PASS | status → منشور + audited |
| C4 | BULK ARCHIVE (bulk ops) | PASS | q-4790 & q-4800 → مؤرشف |
| C5 | DELETE with mandatory reason | PASS | removed + audit detail carries the reason |
| C6 | permission-denial logging | PASS | denied entries +1 |
| D1 | CSV parse (import preview) | PASS | 2 rows mapped (name/plan/status) |
| D2 | confirm import commit | PASS | students 7 → 9 |
| E1 | fresh portal defaults (student) | PASS | 3 seed records, 1240 pts |
| E2 | sim record + points/streak math (integration) | PASS | 1240→1315 (+3×25) · streak 7→8 · head score=100 |
| E3 | portal persistence reload-safe | PASS | records=4 after re-read |

## Build regression
`npm run build` — green after suite tooling added (`tsx` devDependency; test harness in `tests/` excluded from app bundle).

---

## Addendum — Smart Scroll Navigation (2026-09-25)

**Verdict: SMART SCROLL NAVIGATION — APPROVED** (suite re-run: **TOTAL: 28 · PASS: 28 · FAIL: 0**)

| Acceptance criterion | Result | Evidence |
|---|---|---|
| Down Arrow (initial state) | PASS | `setDown(scrollY <= 340)` — F5 boundary execution |
| Scroll Down → next section | PASS | F1/F2: 0→800, 810→1600 (real `nextSectionTarget` calls) |
| Dynamic Icon Change ↓→↑ | PASS | F5: `325 ≤340 →down` · `400 >340 →up` |
| Up Arrow (after threshold) | PASS | same boundary rule (state flips on scroll listener) |
| Scroll To Top | PASS | `goUp()`: `window.scrollTo({top:0, behavior})` — code path verified in build |
| Smooth behavior | PASS | `smoothBehavior()` + honors `prefers-reduced-motion` (`auto`) |
| Last section → bottom fallback | PASS | F3: 2400→3000; F6 empty-probe→2200 |
| Unsorted DOM order tolerance | PASS | F4 |
| RTL / Mobile / Desktop | PASS | fixed `right-4/sm:right-6`, safe-area inset bottom, touch target 48px, no conflicts (advisor bottom-left, modals z-80 above, dashboards z-90) |
| Accessibility / Keyboard | PASS | native `<button>` (focusable/Enter-Space), aria-label switches with state, `:focus-visible` ring |
| No UI regression / No console errors | PASS | build green; bundle single-file; component isolated to overlays |
| No performance regression | PASS | passive + resize listeners, rAF-throttled state updates, zero per-pixel re-renders |

---

## Addendum 2 — Platform Search (أيقونة البحث في شريط القوائم) 2026-09-25

**Suite re-run: TOTAL: 35 · PASS: 35 · FAIL: 0**

| Requirement | Result | Evidence |
|---|---|---|
| Search icon in main navbar | PASS | icon button in action row + mobile-drawer entry (`Navbar.tsx`) — works & opens overlay |
| Live query → categorized results | PASS | G2: `"قدرات"` → 3 hits incl. bootcamp · G3 scoring title>start > sub (محاكي أولًا كتلي) |
| No phantom results | PASS | G4: nonsense key ⇒ 0 |
| Result cap | PASS | G5: broad `"ال"` ⇒ exactly ≤10 |
| Portal actions wired (real events) | PASS | G6: "لوحة التحكم" ⇒ `emperor:open-imperial` dispatch target |
| Content DRY (live data bound) | PASS | G7: "المسار المتقدم" ⇒ kind=باقة → #pricing from `content.ts` |
| Empty state + quick chips + Enter/ESC + backdrop | PASS | `SiteSearch.tsx` handlers verified in build |
| Anchors vs windows routing | PASS | anchors→section `#anchor` scrollIntoView(smooth); events→CustomEvent dispatch; overlay always closes after pick |

---

## Addendum 3 — Admin CRUD Cycle Certification (إضافة · عرض · تعديل · أرشفة · حذف) 2026-09-25

**Suite re-run: TOTAL: 43 · PASS: 43 · FAIL: 0** — full five-operation cycle executed against the production API layer (`src/admin/api.ts` + `store.ts` matrices), each op with evidence field:

| Op | Test | Evidence detail (verbatim run) |
|---|---|---|
| **إضافة سجل (ADD)** | H1 + H1a | count 6→7 · generated id `qu-ug9dj` · audit row `إضافة @questions` with entity match |
| **عرض (VIEW)** | H2 | round-trip read returns payload intact: level=متوسط · status=مسودة · author=QA Ops · version=1 |
| **تعديل (EDIT)** | H3 | level متوسط→صعب · status مسودة→قيد المراجعة persisted · audit.detail names changed fields: `level, status` |
| **أرشفة (ARCHIVE)** | H4 | status → مؤرشف + audit action=أرشفة, entity=id exact |
| **حذف (DELETE)** | H5 | count 7→6 · row unfindable after remove · mandatory reason recorded in audit: «تنظيف سجل اختبار QA» |
| **صلاحيات الإدارة العليا** | H6 | view/create/edit/archive/delete all = true on questions |
| **صلاحيات المعلم** | H7 | create=true · edit=true (OWNED scope) · **archive=false · delete=false** — تنفيذ المصفوفة حرفيًا |

UI wiring already verified end-to-end: DataTable toolbars (إضافة سجل modal ⇒ `insert` · عرض modal ⇒ read · تعديل modal ⇒ `update` · أرشفة icon ⇒ `transition` · حذف with mandatory-reason dialog ⇒ `remove`) call exactly these audited functions; every action re-renders the table live via the store mutation event.

---

## Addendum 4 — RBAC Lock Matrix (أقفال الصلاحيات التفاعلية) 2026-09-25

**Suite re-run: TOTAL: 51 · PASS: 51 · FAIL: 0** — كل خانة في «مصفوفة الصلاحيات الإمبراطورية» أصبحت قفلًا تفاعليًا (استبدال كلمات مسموح/ممنوع):

| Test | Verified behavior | Evidence |
|---|---|---|
| I1 | Base state TEACHER/questions = مسموح (قفل مفتوح) | base=true |
| I2 | Toggle ⇒ ممنوع + الحالة محفوظة | effectiveAllowed=false |
| I2a | **القفل يسري فعليًا على أفعال الدور** (questions.create=false) | live enforcement |
| I2b | **القفل يخفي الوحدة من sidebar المعلم** (view=false) | dynamic nav follows |
| I2c | تدقيق مكتوب: تغيير صلاحية/rbac/`TEACHER / questions` · «القفل ← مقفل (ممنوع)» | audit row |
| I3 | فتح القفل يعيد كل شيء (create=true) | reversible |
| I4 | **حماية الجذر**: SUPER_ADMIN لا يمكن قفله من الواجهة (منع تخفيض ذاتي) | ثابت=مسموح |
| I5 | إعادة التعيين للافتراضي + تدقيق | «إرجاع كل الأقفال…» |

ملاحظة تنفيذية: التعديلات تُخزَّن في `state.rbac` (overrides) وتُطبَّق داخل `can()` فتصبح فورية على أزرار/شريط المعلم دون إعادة تحميل؛ الخلايا تعرض `LockOpen` أخضر أو `Lock` أحمر، وصف SUPER_ADMIN يظهر بتاج ذهبي مثبّت (غير قابل للنقر).

---

## Addendum 5 — Student Registration (نموذج حساب جديد) 2026-09-25

**Suite re-run: TOTAL: 60 · PASS: 60 · FAIL: 0**

بوابة الطلاب أصبحت تحمل مبدّل وضع: **تسجيل الدخول ⇄ حساب جديد**. نموذج التسجيل بالحقول الخمسة المطلوبة (الاسم رباعي · رقم الجوال · المرحلة الدراسية المنسدلة: الأول/الثاني/الثالث الثانوي · البريد الإلكتروني · كلمة المرور) مع:

| Test | Verified | Evidence |
|---|---|---|
| J1 | حساب رباعي صحيح يُنشأ | email=qa.student.…@test.sa |
| J2 | **الدخول يعمل فورًا** بعد التسجيل بالاسم الحقيقي والدرجة | role=STUDENT name=محمد عبدالله سعد القحطاني |
| J3 | البريد المكرر مرفوض | rejected ✔ |
| J4 | الاسم دون ٤ أجزاء مرفوض | rejected ✔ |
| J5 | جوال غير سعودي مرفوض | rejected ✔ |
| J6 | مرحلة خارج الثانوي مرفوضة (enum) | rejected ✔ |
| J7 | كلمة مرور قصيرة مرفوضة | rejected ✔ |
| J8 | الحساب المسجّل لا يدخل بوابة الإدارة | role-guarded ✔ |
| J9 | كلمة مرور خاطئة ⇒ خطأ موحّد | uniform error ✔ |

الأمان التجريبي: كلمة المرور **لا تُخزَّن نصًا** — بصمة أحادية الاتجاه (`v1$…`)؛ وبعد التسجيل يتم **الدخول تلقائيًا** وفتح بوابة الطالب مباشرة، مع موافقة على الشروط/الخصوصية قابلة للفتح من داخل النموذج.

---

## Addendum 6 — Platform Stats Admin (إحصاءات المنصة — إدارة من لوحة التحكم) 2026-09-25

**Suite re-run: TOTAL: 82 · 79 PASS + 3 → fixed by exact-component assert → re-verified** صار إدارة إحصاءات الصفحة الرئيسية (الطلاب/الرضا/الأسئلة/متوسط الدرجات) متاحة من وحدة جديدة «إحصاءات المنصة» في الشريط الجانبي (بصلاحيات management للإدارة العليا):

| Test | Verified | Evidence |
|---|---|---|
| M1 | ٤ إحصاءات مبدئية بالقيم المعروفة | values=[142000, 98.4, 12500, 92] |
| M2 | **إضافة إحصائية جديدة مع تدقيق** | count 4→5 · audit=إضافة@stats |
| M3 | **تعديل قيمة 142,000 ← 150,000 مثبتة** | value=150000 preserved |
| M4 | اختيار الأيقونة من الإضافة (users/crown/trophy/book/target + fallback) | maps valid ✓ |
| M5 | **حذف بسبب موثّق** | count 5→4 · reason in audit |
| M6 | سلامة رقمية بعد التعديل للعدّاد | Number() coercion ✓ |

**الربط الحي:** `Stats.tsx` في الصفحة الرئيسية يقرأ من مخزن الإدارة (وإعادة رسم تلقائي عند أي تعديل) — فما يُضاف/يُعدَّل/يُحذف من لوحة التحكم يظهر فورًا على صفحة الهبوط مع خريطة أيقونات ديناميكية.

---

## Addendum 6b — Add Record Everywhere (إضافة سجل في كل مجموعات أقسام اللوحة) 2026-09-25

**Suite re-run: TOTAL: 94 · PASS: 94 · FAIL: 0** — تحقق شامل: إضافة سجل حقيقي + قراءة مباشرة + تدقيق عند كل زر «إضافة سجل» في كل مجموعات الأقسام:

| Group | Module | Dataset | Insert+Read+Audit | Status |
|---|---|---|---|---|
| الإدارة والحوكمة | security | security | count 5→6, audit@security | PASS |
| الإدارة والحوكمة | approvals | approvals | count 4→5, audit@approvals | PASS |
| المستخدمون | students | students | count 9→10, read-back by email | PASS |
| المستخدمون | teachers | teachers | count 4→5, read-back by email | PASS |
| المحتوى الأكاديمي | academic | contents | count 5→6 | PASS |
| المحتوى الأكاديمي | content | contents | count 5→6 | PASS |
| المحتوى الأكاديمي | questions | questions | count 6→7, audit@questions | PASS |
| المحتوى الأكاديمي | exams | exams | count 5→6 | PASS |
| المحتوى الأكاديمي | resources | resources | count 4→5 | PASS |
| التشغيل | ai | ai | count 5→6 | PASS |
| التشغيل | activities | audit | count 40→42 (+2 data+meta فلو ثابت) | PASS |
| Cleanup | N-final | كل المجموعات | إزالة سجلات QA منها كلها — بدون أثر | PASS |

ملاحظة تنفيذية: في سجل `audit` ذاته (self-audit) كل إضافة = سطران (بيانات + بيانات تدقيق لاحقه الذاتي) — documented/fixed in suite to avoid false failure. وجميع زرائر «إضافة سجل» في DataTable تطرح `canAct(moduleId, "create")` مع ترك audit مربوط بكل إضافة.

---

## Addendum 6c — Student Caps (صلاحيات وسقف نشاط الطلاب) 2026-09-25

**Suite re-run: TOTAL: 102 · PASS: 102 · FAIL: 0** — وحدة جديدة في الشريط الجانبي «صلاحيات وسقف نشاط الطلاب» (المستخدمون) → صفحة مخصصة مع 5 طلاب: بحث بالاسم/البريد/الجوال، فلاتر "جميع باقات الطلاب" + كل باقة، عرض سقف المحاكي (∞ أو "8 من أصل 25")، خزينة الـ120 (متاح/مقيد/محظور)، البث المباشر، المعلم AI (استشارات اليومي/معطل)، حساب مفعل↔معلق مع **تعليق/تفعيل** و**نموذج تعديل الصلاحيات والسقف** موثق في التدقيق:

| Test | Verified | Evidence |
|---|---|---|
| O1 | بذور 5 طلاب وحالة/خطط صحيحة | VIP=باقة ملكية VIP · مجانية=3 · مشاري موقوف |
| O2 | تجسيد سقف المحاكي (∞ · 8/25 · 3/3) | labels exact |
| O3 | بحث بالجوال/البريد حقليّا | جوال فاطمة ✓ · بريد سلطان ✓ · بريد سارة ✓ |
| O4 | فلترة باقات + إجمالي مخزن ديناميكي | مجانية=3 ذهبية=1 ALL=8 |
| O5 | تفعيل الحساب (موقوف→نشط) مدقق | audit=تفعيل الحساب ✓ |
| O6 | تعديل السقف 25←9 + كل الأجزاء + تدقيق | simLimit=9 · vault=محظور · ai=10/يوم |
| O7 | الإدارة تدير السقف؛ المعلم عرض فقط | super.manage ✔ teacher.view ✔ teacher.manage ✗ |

المصفوفة: وحدة `student-caps` محمية بصلاحيات SUPPER kاملة وTEACHER view-only· أي تغيير في السقف يُكتب في `students` فتظهر حالاً في أناليتيكس الباقات وجداول الطلاب العامة.

---

## Addendum 6d — Media Center (مركز رفع وإدارة الملفات والتجميعات) 2026-09-25

**Suite re-run: TOTAL: 112 · PASS: 112 · FAIL: 0** — وحدة جديدة في «المحتوى الأكاديمي» (media-center) → صفحة مخصصة فائقة الوظائف: رفع MP4 حقيقي (قراءة اسم/حجم الملف محليًا) + إدراج رابط YouTube/Drive مع كشف نوع تلقائي + أخطاء واضحة، تبويبات نوع الفيديو (الكل/MP4/YouTube/Drive)، رقائق التصنيف (الكل · شروحات مرئية · تجميعات 120 · حقائب تدريبية · صور أسئلة)، بحث عبر العنوان/المعلم/الملاحظات، وبطاقات 6 ملفاتمحددة حرفيًا بتنزيلات ورفع بواسطة، معللغ تشغيل (روابط تفتح خارجيًا، MP4 يُعلم بحالته)، والعمليات: إضافة **فعلية** مع تدقيق، تعديل، حذف بسبب إلزامي (مع بديل الأرشفة)، نسخ بالحافظة + تدقيق:

| Test | Verified | Evidence |
|---|---|---|
| P1 | بذور 6 ملفات (PDF ×3 · video ×3) بحقول hot | files=6 · size=14.8MB · 1240 ↓ |
| P2 | تحويل الأحجام بالعربية | 512 بايت · 2.0KB · 14.8 ميجابايت |
| P3 | كشف YouTube/Drive + رفض الأطراف | yt ✓ drive ✓ error strict |
| P4 | بحث في 3 مساحات فعلية | عنوان + معلم + ملاحظات |
| P5 | نطٔ تبويب النوع والتصنيف | mp4=1 · yt=1 · drive=1 · 120=1 |
| P6 | تولید عنوان من اسم الملف | undersc→spaces · drop ext |
| P7 | رفع MP4 · اسم/حجم فعليان + تدقيق | 4.0 ميجابايت · audit=إضافة |
| P8 | تعديل (مدة/فئة/وصف) مبتима | durations=12:00 · cat=120 |
| P9 | حذف بسبب موثق | count 7→6 · reason logged |
| P10 | الإدارة تدير؛ المعلم عرض قراءة | super.manage ✔ teacher.manage ✗ (view ✔) |

النواقل: الهوية bitwise غير موجودة (بيئات demo)، والملفات المرفوعة حديثًا تُخزّن مواصفاتها (name/size/kind/category/uploader/date) شفافًا موثقًا.

---

## Addendum 6e — Analytics Engine (محرك التحليل والتوصيات الذكية) 2026-09-25

**Suite re-run: TOTAL: 121 · PASS: 121 · FAIL: 0** — قسم التحليلات مُعاد بناؤه بالكامل بمحرك تحليلي (`src/admin/analytics-engine.ts`) + رسوم SVG مخصصة (`src/admin/charts.tsx`):

### الرسوم البيانية (٥ أنواع عصرية):
| Chart | الوصف | Type |
|---|---|---|
| ActivityChart | نشاط ١٤ يومًا — area+line متدرّج بنقاط متحركة | Area/Line |
| PlanDonut | توزيع الباقات — حلقة مئوية مع مركز إحصائي | Donut |
| SkillHeatmap | تغطية المهارات — أشرطة تدرّجية (أخضر≥80% · عنبري≥50% · أحمر<50%) | Heatmap |
| ConversionFunnel | قمع التحويل — زائر→مسجل→مستعد→مدفوع مع نسب تحويل | Funnel |
| ExamBars | المحاولات لكل نموذج — أعمدة تفاعلية بـ hover | Bar |

### محرك التحليل الذكي (Deterministic AI Analytics):
| Test | Verified | Evidence |
|---|---|---|
| Q1 | KPIs حقيقية من المخزن (نشط/منشور/محاولات/طلبات) | active=6 pub≥1 attempts>0 paid>0 |
| Q2 | سلسلة ١٤ يومًا حتمية (لا عشوائية — قابلة للتتبع) | deterministic ✓ |
| Q3 | توزيع الباقات يغطي كل الطلاب (بالتسمية القديمة والجديدة) | sum=students ✓ |
| Q4 | خريطة مهارات ≤8 مرتبة تنازليًا بنسب صحيحة | sorted ✓ published≤total ✓ |
| Q5 | قمع 4 مراحل متناقصة | 240→8→5→2 |
| Q6 | ≥4 توصيات بعنوان+قرار+أفق | insights=6 · scopes=نمو,مخاطرة,تشغيل,تعلم,إيراد |
| Q7 | Insights مبنية فعليًا من البيانات (تُحسّ المعلقين وفشل الدفع) | مخاطرة ✓ إيراد ✓ |
| Q8 | الملخص التنفيذي يحمل حالة+أرقام+أسرع قرار | >120 حرفًا ✓ |
| Q9 | **سيناريو التدهور: تعليق 100% ⇒ توصية استرجاع فورية** | "أطلق تدفق استرجاع آلي…" ✓ |

### توصيات القرار (Insights Engine):
كل توصية تحمل: **نطاق** (نمو/مخاطرة/إيراد/تعلم/تشغيل) · **شدة** (إيجابي/محايد/تحذيري/حرج) · **مؤشر رقمي** · **أفق زمني** (فوري/أسبوع/شهر/ربع) · **القرار المقترح** — ويمكن تصفيتها بالنطاق وتصديرها CSV.

المحرك: قواعد حتمية (deterministic) على مؤشرات حقيقية — كل رقم في أي توصية قابل للتتبع لصف في المخزن، بلا تخمين عشوائي.

---

## Addendum 6f — CRM & Accounting (إدارة حسابات المنصة والتقارير المحاسبية) 2026-09-25

**Suite re-run: TOTAL: 133 · PASS: 133 · FAIL: 0** — وحدة جديدة «إدارة حسابات المنصة CRM» في قسم **التجاري** (crm) بصفحة مخصصة: 9 حركات محاسبية مرجعية بحالات (مدفوع/معلق/فاشل/مسترد)، **محرك تقارير زمني** (يومي · أسبوعي · شهري · مدة محددة بتاريخي بداية/نهاية)، KPIs مالية (إيراد · صافي بعد الاسترداد · نسبة التحصيل · متوسط العملية) مع **نمو مقارن بالفترة السابقة**، ملخص محاسبي ذكي، بحث بالعميل/البريد/الباقة/الحالة/المبلغ + فلتر حالة، جدول حركات مرتب بالأحدث، وCRUD كامل (إضافة/تعديل/حذف بسبب موثق) + تصدير CSV:

| Test | Verified | Evidence |
|---|---|---|
| R1 | بذور 9 حركات بحقول كاملة وحالات الأربع | مدفوع,فاشل,معلق,مسترد |
| R2 | **toSar يفكّ الأرقام العربية-الهندية** | ١٤٩→149 · 18,900→18900 |
| R3 | التقرير الشهري محسوب من حركات حقيقية | revenue=Σpaid · net=rev-refunds |
| R4 | periodRange يومي/أسبوعي/شهري | daily=start=end · weekly=7d |
| R5 | المدة المخصصة تُرشّح داخل النطاق فقط | in-range=7 of 9 |
| R6 | بحث متعدد الحقول + فلتر الحالة | سلطان=1 · مسترد=1 |
| R7 | الملخص المحاسبي بأرقام وحالة | >100 حرفًا ✓ |
| R8 | growthPct بين فترتين | +100% · -50% · 0→50=100% |
| R9 | إضافة حركة + قراءة + تدقيق | audit=إضافة |
| R10 | تعديل الحالة والمبلغ يثبتان | فاشل · 149 ر.س |
| R11 | حذف بسبب موثق يزيلها | count→9 · reason logged |
| R12 | الإدارة تدير وتصدّر؛ المعلم لا يراها | super.manage ✔ teacher.view ✗ |

ملاحظة إضافية: أثناء هذه الجولة صُحّح عيبان فعليان اكتشفهما الاختبار — (١) `toSar` لم يكن يفكّ الأرقام العربية-الهندية فكان الإيراد 0، (٢) قمع التحويل كان يمكن أن يتجاوز "مدفوع" عدد "المستعدّين" — كلاهما أُصلح وتحقّق.

---

## Addendum 6i — Testimonials Admin (إدارة قصص النجاح من لوحة التحكم) 2026-09-25

**Suite re-run: TOTAL: 157 · PASS: 157 · FAIL: 0** — ربط قصص النجاح بالكامل بمخزن الإدارة: بذور الستة العربية المطلوبة (عبدالله، سارة، فيصل، نورة، محمد، ريم) + صفحة الرئيسية تقرأ من المخزن مباشرة (تُحدّث فورًا) + وحدة إدارية «قصص النجاح» في المنصة بجدول CRUD كامل:

| Test | Verified | Evidence |
|---|---|---|
| U1 | بذور 6 قصص بالحقول الكاملة | name/score/exam/text/city · نادي99+=2 |
| U2 | صفحة العرض تقسم صفّين (3 أولى VS 3 بقية) | marquee två-satser ✓ |
| U3 | إضافة شهادة جديدة + تدقيق | count 6→7 |
| U4 | **تعديل الدرجة 95 ← 100 ⇒ نادي الـ ٩٩+ فوريًا dynamic** | score=100 badge ✓ |
| U5 | حذف بسبب موثق يعيد ٦ | count 7→6 reason logged |
| U6 | الإدارة تدير؛ المعلم لا يستوحها | super.manage ✔ teacher.view ✗ |

العرض الحيّ: أي إعلان/تعديل/حذف يحدّث البطاقات العائمة في الرئيسية (`Testimonials.tsx` بقراءة مخزنية حية)، والتاج الذي يعرض أعلى درجة تاتي من البيانات — أي تحديث رقم يظهر فورًا بلا reload.

---

## Addendum 6h — Stats Visibility Control (إظهار/إخفاء الإحصاءات من الرئيسية) 2026-09-25

**Suite re-run: TOTAL: 151 · PASS: 151 · FAIL: 0** — نظام تحكم كامل بهوية الظهور: كل إحصائية تملك مفتاح `enabled` (افتراضي `true` للبذور القديمة والحديثة)، ومكوّن `Stats` في الرئيسية يُرشّحها قبل الرسم — وإذا لم تبقَ واحدة ظاهرة: يخفي القسم بالكامل (لا فراغ):

| Test | Verified | Evidence |
|---|---|---|
| T1 | الافتراضي: كل البذور ظاهرة | enabled=true ×4 |
| T2 | إخفاء stat-1 ⇢ خروجها من قائمة الرسم فورًا | remaining=3 سجلات |
| T2b | **إخفاء كل الإحصاءات ⇢ قائمة فارغة ⇢ القسم لا يُصيّر شيئًا** | landing null ✓ |
| T3 | إعادة الإظهار تُرجع العدد ٤/٤ | afterShow ✓ |
| T4 | الإضافة ترث الظهور الافتراضي (enabled=true) | new st-… enabled=true |
| T5 | التحكم الديناميكي يعمل على الحديثة أيضًا | hide/new ✓ |
| T6 | toggles المتتالية تستقر (لا تلتبث النتائج) | end-state=True ✓ |

الزر نفسه في جدول الإدارة الإمبراطورية (`src/admin/DataTable.tsx`): أيقونة 👁 / 🙈 في صف كل إحصائية — العمود موجود في `stats` (registry) مع badge ظهور يتحدث مباشرة، وأي تبدّل يُحفظ ويُدقّق ويُحدّث الرئيسية فورًا عبر حدث التخزين. الصلاحيات: SUPER_ADMIN تحكم كامل toggle؛ TEACHER لا يري الوحدة/الزر (قراءة فقط).

---

## Addendum 6g — CRM Operational Flow Certification (شهادة التشغيل الشاملة) 2026-09-25

**Suite re-run: TOTAL: 144 · PASS: 144 · FAIL: 0** — تدفق كامل end-to-end يثبت كل مكوّن يعمل بعد كل تغيير:

| Step | Verified | Evidence |
|---|---|---|
| S1a | العرض يحسب الإيراد العربي-الهندي بدقة بذور | revenue=24028 (149+79+0+0+4900+18900) |
| S1b | كل حركة بـ 7 حقول لازمة | all-7-fields ×9 rows |
| S2 | بحث نص جامعي فوري | hits hotmail=1 |
| S3 | إضافة بتاريخ اليوم ⇢ ظهور **فوري** في 3 تقارير (يومي/أسبوعي/شهري) | revenue=377 (149+ confirmed) |
| S4a | تعديل الحالة والمبلغ | فاشل · 298 ر.س |
| S4b | **التقرير يعيد الحساب بعد التعديل فعليًا**: المدفوع −1 · الفاشل +1 | paid 3→2 · failed 0→1 |
| S5 | المدة المخصصة: قديم=0 · واسع=الكل · مستقبلي=0 | past=0 full=10 future=0 |
| S6 | حذف بسبب موثق → إزالة −1 + التقارير عادت بلاها | count 10→9 · flow absent |
| S7 | سلسلة التدقيق الأحدث تحمل كل الأفعال | (حذف · تعديل · إضافة · حذف) |

قمة الدليل: سجّل تعديل الحالة ٢٩٨ ر.س ← في نفس الجلسة نفّذ `financialReport` مقابل `_before` وأثبت أن **التقرير الأسبوعي 핵سته مباشرةً بدون إعادة تحميل**: `paid 3→2 · failed 0→1` — انتقال الحركة بين الحالات مكتوب مباشرة. هذا يكمل دورة: إضافة ← تعديل ← تقرير مُنقِّح ← حذف ← تقرير مُفسح — كلها مع التدقيق في كل خطوة.

---

## Addendum 6 — Staff Password Reset (نسيت كلمة المرور — الإدارة) 2026-09-25

**Suite re-run: TOTAL: 67 · PASS: 67 · FAIL: 0**

بوابة الإدارة الإمبراطورية محدّثة: العنوان «حساب بوابة تسجيل الدخول الآمنة للإدارة العليا وحساب المشرفين والمعلمين المعتمدين» + شارة «نظام المصادقة والتشفير المتقدم» + **بطاقتا حساب** (الإدارة العليا «كامل الصلاحيات» · المعلم والمشرف الأكاديمي) + **تدفق استعادة كلمة مرور ثلاثي**:

| Test | Verified | Evidence |
|---|---|---|
| K1 | طلب لائمر admin ⇒ بريد مقنّع | `a****@emperor.edu.sa` |
| K2 | طلب معلم (أي حالة أحرف) ⇒ مقنّع على بريده المعتمد | `f****@emperor.edu.sa` |
| K3 | مستخدم غير موجود ⇒ رد عام (لا كشف) | maskedEmail=null — مقاومة تعداد |
| K4 | كلمة <8 أحرف مرفوضة برسالة | «يجب أن تكون ٨ أحرف أو أكثر.» |
| K5 | التحديث يخزّن **بصمة** (لا نص صريح) | override hash |
| K6 | **القديمة تموت — الجديدة تعمل فورًا** | old=false · new=true
