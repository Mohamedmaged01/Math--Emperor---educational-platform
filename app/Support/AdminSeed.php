<?php

namespace App\Support;

/** Initial imperial-workspace data (mirror of the `seed` object in src/admin/store.ts). */
class AdminSeed
{
    public static function datasets(): array
    {
        return [
            'students' => [
                ['id' => 'st-1', 'name' => 'سلطان بن محمد الدوسري', 'email' => 'sultan.aldawsari@gmail.com', 'phone' => '0501234567', 'grade' => 'ثالث ثانوي', 'plan' => 'باقة ملكية VIP', 'simsUsed' => 0, 'simsLimit' => '∞', 'simUnlimited' => true, 'vault' => 'متاح', 'live' => 'متاح', 'aiLimit' => 100, 'status' => 'نشط', 'enrolled' => '2026-08-12'],
                ['id' => 'st-2', 'name' => 'فاطمة بنت ناصر القحطاني', 'email' => 'fatima.alqahtani@outlook.com', 'phone' => '0559876543', 'grade' => 'ثالث ثانوي', 'plan' => 'باقة ذهبية', 'simsUsed' => 8, 'simsLimit' => 25, 'simUnlimited' => false, 'vault' => 'متاح', 'live' => 'متاح', 'aiLimit' => 30, 'status' => 'نشط', 'enrolled' => '2026-08-20'],
                ['id' => 'st-3', 'name' => 'عبدالرحمن بن خالد العتيبي', 'email' => 'abdulrahman.otaibi@gmail.com', 'phone' => '0543322114', 'grade' => 'ثانوي', 'plan' => 'باقة مجانية', 'simsUsed' => 4, 'simsLimit' => 10, 'simUnlimited' => false, 'vault' => 'متاح', 'live' => 'مقيد', 'aiLimit' => 15, 'status' => 'نشط', 'enrolled' => '2026-09-01'],
                ['id' => 'st-4', 'name' => 'سارة بنت عبدالله السبيعي', 'email' => 'sara.subaie@hotmail.com', 'phone' => '0567788990', 'grade' => 'ثانوي', 'plan' => 'باقة مجانية', 'simsUsed' => 2, 'simsLimit' => 3, 'simUnlimited' => false, 'vault' => 'محظور', 'live' => 'مقيد', 'aiLimit' => 5, 'status' => 'نشط', 'enrolled' => '2026-09-10'],
                ['id' => 'st-5', 'name' => 'مشاري بن فهد الحربي', 'email' => 'meshari.harbi@gmail.com', 'phone' => '0531122334', 'grade' => 'ثالث ثانوي', 'plan' => 'باقة مجانية', 'simsUsed' => 3, 'simsLimit' => 3, 'simUnlimited' => false, 'vault' => 'محظور', 'live' => 'مقيد', 'aiLimit' => 0, 'status' => 'موقوف', 'enrolled' => '2026-07-25'],
            ],
            'teachers' => [
                ['id' => 'tc-1', 'name' => 'أ. فهد الزهراني', 'email' => 'fahad.zahrani@emperor.edu.sa', 'subject' => 'الرياضيات — كمي متقدم', 'students' => 342, 'groups' => 6, 'status' => 'معتمد', 'since' => '2025-11-03'],
                ['id' => 'tc-2', 'name' => 'أ. سارة العمر', 'email' => 'sara.a@emperor.edu.sa', 'subject' => 'قدرات لفظي — استيعاب مقروء', 'students' => 281, 'groups' => 5, 'status' => 'معتمد', 'since' => '2025-12-14'],
                ['id' => 'tc-3', 'name' => 'د. عبدالله المطيري', 'email' => 'a.mutairi@emperor.edu.sa', 'subject' => 'تحصيلي — فيزياء وكيمياء', 'students' => 198, 'groups' => 4, 'status' => 'معتمد', 'since' => '2026-01-07'],
                ['id' => 'tc-4', 'name' => 'أ. خالد الحربي', 'email' => 'k.harbi@emperor.edu.sa', 'subject' => 'هندسة وتحليل', 'students' => 0, 'groups' => 0, 'status' => 'طلب اعتماد — قيد المراجعة', 'since' => '2026-09-20'],
            ],
            'questions' => [
                ['id' => 'q-4821', 'type' => 'اختيار من متعدد', 'skill' => 'معادلات تربيعية', 'level' => 'صعب', 'author' => 'أ. فهد الزهراني', 'status' => 'قيد المراجعة', 'updated' => '2026-09-24', 'version' => 3],
                ['id' => 'q-4822', 'type' => 'تناظر لفظي', 'skill' => 'العلاقات', 'level' => 'متوسط', 'author' => 'أ. سارة العمر', 'status' => 'قيد المراجعة', 'updated' => '2026-09-24', 'version' => 1],
                ['id' => 'q-4790', 'type' => 'اختيار من متعدد', 'skill' => 'النسب والتناسب', 'level' => 'سهل', 'author' => 'محرك المحتوى', 'status' => 'منشور', 'updated' => '2026-09-22', 'version' => 5],
                ['id' => 'q-4827', 'type' => 'تحصيلي', 'skill' => 'الاتزان الكيميائي', 'level' => 'صعب', 'author' => 'د. عبدالله المطيري', 'status' => 'معتمد — بانتظار النشر', 'updated' => '2026-09-23', 'version' => 2],
                ['id' => 'q-4831', 'type' => 'اختيار من متعدد', 'skill' => 'مساحة المقاطع الدائرية', 'level' => 'متوسط', 'author' => 'أ. فهد الزهراني', 'status' => 'مسودة', 'updated' => '2026-09-25', 'version' => 1],
                ['id' => 'q-4782', 'type' => 'عدد إجابات', 'skill' => 'لوغاريتمات', 'level' => 'صعب', 'author' => 'محرك المحتوى', 'status' => 'مؤرشف', 'updated' => '2026-05-11', 'version' => 4],
                ['id' => 'q-4800', 'type' => 'استيعاب مقروء', 'skill' => 'النصوص العلمية', 'level' => 'متوسط', 'author' => 'أ. سارة العمر', 'status' => 'منشور', 'updated' => '2026-09-19', 'version' => 2],
            ],
            'exams' => [
                ['id' => 'ex-11', 'title' => 'محاكي قدرات — نموذج سبتمبر الكامل', 'sections' => 6, 'questions' => 96, 'duration' => '١٥٠ دقيقة', 'status' => 'منشور', 'attempts' => 4812],
                ['id' => 'ex-12', 'title' => 'تحصيلي — فيزياء الوحدة الأولى', 'sections' => 2, 'questions' => 40, 'duration' => '٦٠ دقيقة', 'status' => 'منشور', 'attempts' => 1931],
                ['id' => 'ex-13', 'title' => 'قدرات تجريبي — كمي مركز', 'sections' => 3, 'questions' => 33, 'duration' => '٧٥ دقيقة', 'status' => 'قيد البناء', 'attempts' => 0],
                ['id' => 'ex-14', 'title' => 'نموذج ٩٥+ — القمم العشرة', 'sections' => 1, 'questions' => 20, 'duration' => '٤٥ دقيقة', 'status' => 'مسودة', 'attempts' => 0],
                ['id' => 'ex-15', 'title' => 'تحصيلي — نموذج أغسطس', 'sections' => 4, 'questions' => 80, 'duration' => '١٢٠ دقيقة', 'status' => 'مؤرشف', 'attempts' => 12405],
            ],
            'contents' => [
                ['id' => 'cn-1', 'title' => 'درس: فرق المربعين بعشرة طرق حل', 'kind' => 'فيديو', 'status' => 'منشور', 'owner' => 'أ. فهد الزهراني', 'size' => '824 MB', 'updated' => '2026-09-21'],
                ['id' => 'cn-2', 'title' => 'مذكرة: خرائط الذهن للتناظر اللفظي', 'kind' => 'PDF', 'status' => 'قيد المراجعة', 'owner' => 'أ. سارة العمر', 'size' => '12 MB', 'updated' => '2026-09-23'],
                ['id' => 'cn-3', 'title' => 'عرض: الاتزان الكيميائي تفاعلي', 'kind' => 'PowerPoint', 'status' => 'معتمد — بانتظار النشر', 'owner' => 'د. عبدالله المطيري', 'size' => '48 MB', 'updated' => '2026-09-22'],
                ['id' => 'cn-4', 'title' => 'تجميعة سبتمبر — كمي (١٨٠ سؤالًا)', 'kind' => 'تجميعة', 'status' => 'منشور', 'owner' => 'فريق المحتوى', 'size' => '22 MB', 'updated' => '2026-09-10'],
                ['id' => 'cn-5', 'title' => 'درس مسجل: الهندسة الفضائية أساسيات', 'kind' => 'فيديو', 'status' => 'مسودة', 'owner' => 'أ. خالد الحربي', 'size' => '1.2 GB', 'updated' => '2026-09-24'],
            ],
            'resources' => [
                ['id' => 'rs-1', 'name' => 'تجميعة-القدرات-سبتمبر.pdf', 'type' => 'PDF', 'size' => '22 MB', 'downloads' => 12840, 'status' => 'منشور', 'visibility' => 'مدفوع فقط'],
                ['id' => 'rs-2', 'name' => 'قوالب-خطة-الانطلاق.xlsx', 'type' => 'Excel', 'size' => '3 MB', 'downloads' => 4291, 'status' => 'منشور', 'visibility' => 'الجميع'],
                ['id' => 'rs-3', 'name' => 'شرائح-الفيزياء-المحلل.pptx', 'type' => 'PowerPoint', 'size' => '61 MB', 'downloads' => 908, 'status' => 'مسودة', 'visibility' => 'المعلمون'],
                ['id' => 'rs-4', 'name' => 'مذكرات-التحصيلي-الذهبية.pdf', 'type' => 'PDF', 'size' => '89 MB', 'downloads' => 30200, 'status' => 'منشور', 'visibility' => 'مدفوع فقط'],
            ],
            'orders' => [
                ['id' => 'or-9141', 'customer' => 'سلطان الدوسري', 'email' => 'sultan.aldawsari@gmail.com', 'plan' => 'VIP — المسار الاحترافي', 'amount' => '١٤٩ ر.س', 'status' => 'مدفوع', 'method' => 'مدى', 'date' => '2026-09-26'],
                ['id' => 'or-9155', 'customer' => 'فاطمة القحطاني', 'email' => 'fatima.alqahtani@outlook.com', 'plan' => 'Gold — الانطلاق', 'amount' => '٧٩ ر.س', 'status' => 'مدفوع', 'method' => 'Apple Pay', 'date' => '2026-09-24'],
                ['id' => 'or-9162', 'customer' => 'عبدالرحمن العتيبي', 'email' => 'abdulrahman.otaibi@gmail.com', 'plan' => 'Gold — الانطلاق', 'amount' => '٧٩ ر.س', 'status' => 'فاشل', 'method' => 'فيزا', 'date' => '2026-09-23'],
                ['id' => 'or-9165', 'customer' => 'مدرسة الرواد الأهلية', 'email' => 'info@alrowad.edu.sa', 'plan' => 'مؤسسي — سنة', 'amount' => '١٨٩٠٠ ر.س', 'status' => 'مدفوع', 'method' => 'تحويل بنكي', 'date' => '2026-09-14'],
                ['id' => 'or-9170', 'customer' => 'سارة السبيعي', 'email' => 'sara.subaie@hotmail.com', 'plan' => 'مجاني — تأسيس', 'amount' => '٠ ر.س', 'status' => 'مدفوع', 'method' => '—', 'date' => '2026-09-10'],
                ['id' => 'or-9172', 'customer' => 'مشاري الحربي', 'email' => 'meshari.harbi@gmail.com', 'plan' => 'مجاني — تأسيس', 'amount' => '٠ ر.س', 'status' => 'مدفوع', 'method' => '—', 'date' => '2026-08-28'],
                ['id' => 'or-9175', 'customer' => 'نورة المطيري', 'email' => 'noura.m@student.sa', 'plan' => 'Gold — الانطلاق', 'amount' => '٧٩ ر.س', 'status' => 'معلق', 'method' => 'مدى', 'date' => '2026-09-25'],
                ['id' => 'or-9178', 'customer' => 'ريم الشمري', 'email' => 'reem.sh@student.sa', 'plan' => 'Gold — الانطلاق', 'amount' => '٧٩ ر.س', 'status' => 'مسترد', 'method' => 'مدى', 'date' => '2026-09-18'],
                ['id' => 'or-9180', 'customer' => 'طالب مؤسسي جديد', 'email' => 'new@school.sa', 'plan' => 'مؤسسي — فصل', 'amount' => '٤٩٠٠ ر.س', 'status' => 'مدفوع', 'method' => 'تحويل بنكي', 'date' => '2026-08-20'],
            ],
            'notifications' => [
                ['id' => 'nt-1', 'audience' => 'الجميع', 'title' => 'بدء الدورة الجديدة من محاكي السبت', 'channel' => 'داخل المنصة + بريد', 'status' => 'مكتمل', 'scheduled' => '2026-09-20'],
                ['id' => 'nt-2', 'audience' => 'المشتركون الذهبيون', 'title' => 'تجميعة سبتمبر الجديدة متاحة الآن', 'channel' => 'داخل المنصة', 'status' => 'قيد الإرسال', 'scheduled' => '2026-09-25'],
                ['id' => 'nt-3', 'audience' => 'المعلمون', 'title' => 'مراجعة ٣٤ سؤالًا جديدًا مطلوبة قبل الخميس', 'channel' => 'بريد', 'status' => 'مجدول', 'scheduled' => '2026-09-27'],
                ['id' => 'nt-4', 'audience' => 'طلاب الباقة المجانية', 'title' => 'ترقية إلى Gold بخصم الترم ٢٠٪', 'channel' => 'داخل المنصة', 'status' => 'مسودة', 'scheduled' => '—'],
            ],
            'security' => [
                ['id' => 'sc-1', 'type' => 'محاولات دخول فاشلة', 'actor' => 'IP 5.62.…', 'severity' => 'متوسط', 'count' => 14, 'status' => 'مراقبة', 'at' => '2026-09-25 08:10'],
                ['id' => 'sc-2', 'type' => 'تغيير صلاحيات حساس', 'actor' => 'admin', 'severity' => 'عالٍ', 'count' => 1, 'status' => 'موثق', 'at' => '2026-09-24 21:40'],
                ['id' => 'sc-3', 'type' => 'تصدير بيانات', 'actor' => 'finance.mgr', 'severity' => 'منخفض', 'count' => 3, 'status' => 'موثق', 'at' => '2026-09-24 16:02'],
                ['id' => 'sc-4', 'type' => 'قفل حساب مؤقت', 'actor' => 'محارب القوى', 'severity' => 'عالٍ', 'count' => 1, 'status' => 'مُقفل ٣٠د', 'at' => '2026-09-23 12:33'],
                ['id' => 'sc-5', 'type' => 'تجاوز محاولة Cross-Scope', 'actor' => 'token τ-884', 'severity' => 'حرج', 'count' => 1, 'status' => 'محظور وموثق', 'at' => '2026-09-22 19:15'],
            ],
            'ai' => [
                ['id' => 'ai-1', 'user' => 'نورة المطيري', 'mode' => 'شرح مفهوم', 'tokens' => 2340, 'status' => 'مكتمل', 'at' => '2026-09-25 09:41'],
                ['id' => 'ai-2', 'user' => 'سلطان الدوسري', 'mode' => 'تحليل خطأ', 'tokens' => 3120, 'status' => 'مكتمل', 'at' => '2026-09-25 09:12'],
                ['id' => 'ai-3', 'user' => 'أ. فهد الزهراني', 'mode' => 'توليد سؤال', 'tokens' => 5410, 'status' => 'بانتظار مراجعة بشرية', 'at' => '2026-09-25 08:55'],
                ['id' => 'ai-4', 'user' => 'محمد الغامدي', 'mode' => 'خطة دراسة', 'tokens' => 2890, 'status' => 'مكتمل', 'at' => '2026-09-24 22:03'],
                ['id' => 'ai-5', 'user' => 'فاطمة القحطاني', 'mode' => 'تلميح هامشي', 'tokens' => 1180, 'status' => 'مرفوض — خارج الصلاحية', 'at' => '2026-09-24 20:40'],
            ],
            'approvals' => [
                ['id' => 'ap-1', 'kind' => 'اعتماد سؤال', 'title' => 'q-4827 — الاتزان الكيميائي (صعب)', 'requester' => 'د. عبدالله المطيري', 'risk' => 'متوسط', 'status' => 'بانتظار القرار', 'at' => '2026-09-23'],
                ['id' => 'ap-2', 'kind' => 'اعتماد معلم', 'title' => 'طلب اعتماد — أ. خالد الحربي', 'requester' => 'النظام', 'risk' => 'عالٍ', 'status' => 'بانتظار القرار', 'at' => '2026-09-20'],
                ['id' => 'ap-3', 'kind' => 'نشر محتوى', 'title' => 'عرض الاتزان الكيميائي التفاعلي', 'requester' => 'د. عبدالله المطيري', 'risk' => 'منخفض', 'status' => 'بانتظار القرار', 'at' => '2026-09-22'],
                ['id' => 'ap-4', 'kind' => 'استرداد مالي', 'title' => 'or-9178 — ٧٩ ر.س', 'requester' => 'finance.mgr', 'risk' => 'عالٍ', 'status' => 'بانتظار القرار', 'at' => '2026-09-22'],
            ],
            'flags' => [
                ['id' => 'fl-1', 'feature' => 'غرفة الـ ٩٥+', 'enabled' => true, 'target' => 'باقات VIP/Gold', 'owner' => 'النظام'],
                ['id' => 'fl-2', 'feature' => 'المستشار الذكي AI Pro', 'enabled' => true, 'target' => 'الجميع', 'owner' => 'النظام'],
                ['id' => 'fl-3', 'feature' => 'تحدي النخبة الأسبوعي', 'enabled' => false, 'target' => 'قريبًا', 'owner' => 'منتج'],
                ['id' => 'fl-4', 'feature' => 'وضع المراجعة الليلية', 'enabled' => true, 'target' => 'تجريبي ٢٠٪', 'owner' => 'منتج'],
            ],
            'stats' => [
                ['id' => 'stat-1', 'label' => 'طالب وطالبة', 'value' => 142000, 'suffix' => '+', 'decimals' => 0, 'iconKey' => 'crown', 'enabled' => true],
                ['id' => 'stat-2', 'label' => 'نسبة رضا المشتركين', 'value' => 98.4, 'suffix' => '%', 'decimals' => 1, 'iconKey' => 'trophy', 'enabled' => true],
                ['id' => 'stat-3', 'label' => 'سؤال في بنك الأسئلة', 'value' => 12500, 'suffix' => '+', 'decimals' => 0, 'iconKey' => 'book', 'enabled' => true],
                ['id' => 'stat-4', 'label' => 'متوسط درجات طلابنا', 'value' => 92, 'suffix' => '', 'decimals' => 0, 'iconKey' => 'target', 'enabled' => true],
            ],
            'media' => [
                ['id' => 'md-1', 'title' => 'تجميع_الـ_120_نموذج_المحوسب_1446_النسخة_الذهبية.pdf', 'kind' => 'file', 'category' => 'تجميعات 120', 'size' => '14.8 ميجابايت', 'uploader' => 'الإدارة الأكاديمية', 'date' => '2026-09-18', 'downloads' => 1240, 'description' => '', 'notes' => '', 'url' => ''],
                ['id' => 'md-2', 'title' => 'شفرة_الإمبراطور_لقوانين_الكمي_السريعة.pdf', 'kind' => 'file', 'category' => 'حقائب تدريبية', 'size' => '6.5 ميجابايت', 'uploader' => 'أ. فهد الزهراني', 'date' => '2026-09-16', 'downloads' => 3120, 'description' => '', 'notes' => '', 'url' => ''],
                ['id' => 'md-3', 'title' => 'رسم_هندسي_الدائرة_والمثلث_المتطابق.png', 'kind' => 'file', 'category' => 'صور أسئلة', 'size' => '1.2 ميجابايت', 'uploader' => 'قسم الرسوم والتصميم', 'date' => '2026-09-17', 'downloads' => 450, 'description' => 'رسم توضيحي للدائرة والمثلث المتطابق', 'notes' => '', 'url' => ''],
                ['id' => 'md-4', 'title' => 'شرح_تكتيك_شطب_الخيارات_في_استيعاب_المقروء.mp4', 'kind' => 'video-mp4', 'category' => 'شروحات مرئية', 'size' => '—', 'duration' => '24:15', 'uploader' => 'د. خالد العمري', 'date' => '2026-09-15', 'downloads' => 0, 'description' => 'شرح تحليلي شامل لكيفية اكتشاف العلاقات الخفية والبدائل المضللة في اختبار القدرات اللفظي.', 'notes' => '', 'url' => ''],
                ['id' => 'md-5', 'title' => 'استراتيجية_حل_مسائل_السرعة_والمسافة_في_15_ثانية', 'kind' => 'video-yt', 'category' => 'شروحات مرئية', 'size' => '—', 'duration' => '18:40', 'uploader' => 'أ. فهد الزهراني', 'date' => '2026-09-18', 'downloads' => 0, 'description' => 'شرح تكتيكي لحل مسائل حركة الجسمين في نفس الاتجاه وعكس الاتجاه ذهنياً بدون معادلات طويلة.', 'notes' => '', 'url' => 'https://www.youtube.com/watch?v=qud-15-sec'],
                ['id' => 'md-6', 'title' => 'المحاضرة_التأسيسية_الشاملة_للهندسة_والدائرة_1446', 'kind' => 'video-drive', 'category' => 'شروحات مرئية', 'size' => '—', 'duration' => '36:20', 'uploader' => 'أ. طارق الشمري', 'date' => '2026-09-14', 'downloads' => 0, 'description' => 'تسجيل القاعة الافتراضية مع حل 40 نموذجاً من أسئلة قياس المحوسبة 1446هـ.', 'notes' => '', 'url' => 'https://drive.google.com/file/d/geom1446/preview'],
            ],
            'testimonials' => [
                ['id' => 'ts-1', 'name' => 'عبدالله المطيري', 'score' => 99, 'exam' => 'قدرات', 'text' => 'المحاكي مطابق لأجواء قياس تمامًا — نفس التوقيت ونفس التوزيع. دخلت الاختبار وأنا حاس إني معيده عادي. جبت ٩٩ والحمد لله.', 'city' => 'الرياض'],
                ['id' => 'ts-2', 'name' => 'سارة العتيبي', 'score' => 98, 'exam' => 'تحصيلي', 'text' => 'الخطة الذكية غيّرت طريقة مذاكرتي بالكامل. كل يوم أعرف وش أذاكر بالضبط، والتقارير توريني نقاط ضعفي أول بأول.', 'city' => 'جدة'],
                ['id' => 'ts-3', 'name' => 'فيصل الشمري', 'score' => 97, 'exam' => 'قدرات', 'text' => 'كنت ضعيف في القسم الكمي، تدربت على بنك الأسئلة شهرين ونصف بنظام المستويات. شرح كل سؤال مفصّل وواضح.', 'city' => 'الدمام'],
                ['id' => 'ts-4', 'name' => 'نورة القحطاني', 'score' => 100, 'exam' => 'قدرات', 'text' => 'جبت ١٠٠ في القدرات! سرّي؟ المحاكي الأسبوعي وخاصية مراجعة الأخطاء. ما طلعت من اختبار إلا وأنا متأكدة من إجاباتي.', 'city' => 'مكة المكرمة'],
                ['id' => 'ts-5', 'name' => 'محمد الغامدي', 'score' => 96, 'exam' => 'تحصيلي', 'text' => 'تجميعات التحصيلي محدثة دورة بدورة، والأسئلة مرتبة حسب نسب التكرار. وفّر عليّ شهور من البحث والتشتت.', 'city' => 'أبها'],
                ['id' => 'ts-6', 'name' => 'ريم الدوسري', 'score' => 98, 'exam' => 'قدرات', 'text' => 'أفضل شيء خاصية توقيت السؤال — تعلمت أحل في أقل من دقيقة للسؤال. إدارة الوقت كانت أكبر مشكلة عندي وانحلّت.', 'city' => 'المدينة المنورة'],
            ],
        ];
    }

    public static function settings(): array
    {
        return [
            'cms' => [
                'heroLine1' => 'أتقن الرياضيات،',
                'heroLine2Gold' => 'وتوّج درجتك بالـ 100%',
                'heroLine3' => 'في القدرات والتحصيلي',
                'published' => false,
            ],
            'marketing' => [
                'adsenseEnabled' => false,
                'publisherId' => '',
                'autoAds' => true,
                'testMode' => false,
                'maxAdsPerPage' => 2,
                'placements' => ['HOME_BOTTOM' => true, 'PUBLIC_CONTENT_TOP' => true, 'PUBLIC_CONTENT_BOTTOM' => true],
                'policy' => 'Development → Disabled · Testing → Disabled · Staging → Disabled by default · Production → Admin controlled',
                'consentNote' => 'خصوصية الطالب أولًا: لا إعلانات داخل الاختبارات أو المودالات أو علب الاختبارات أو مصاشقة الشرح التعليمي الحساس.',
            ],
            'seoOverrides' => new \stdClass(),
            'rbac' => new \stdClass(),
        ];
    }
}
