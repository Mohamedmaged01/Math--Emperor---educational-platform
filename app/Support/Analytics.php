<?php

namespace App\Support;

/** Analytics engine — KPIs, chart series and deterministic decision insights. */
class Analytics
{
    private static function num(mixed $v): float
    {
        return is_numeric($v) ? (float) $v : 0.0;
    }

    private static function digits(mixed $v): float
    {
        $s = preg_replace('/[^\d]/u', '', (string) $v);

        return $s === '' ? 0.0 : (float) $s;
    }

    public static function kpis(array $d): array
    {
        $st = fn ($r) => (string) ($r['status'] ?? '');
        $paid = array_filter($d['orders'], fn ($o) => $st($o) === 'مدفوع');
        $failed = array_filter($d['orders'], fn ($o) => str_contains($st($o), 'فشل'));

        return [
            'activeStudents' => count(array_filter($d['students'], fn ($s) => $st($s) === 'نشط')),
            'suspendedStudents' => count(array_filter($d['students'], fn ($s) => $st($s) === 'موقوف')),
            'publishedQuestions' => count(array_filter($d['questions'], fn ($q) => $st($q) === 'منشور')),
            'pendingQuestions' => count(array_filter($d['questions'], fn ($q) => str_contains($st($q), 'مراجعة') || str_contains($st($q), 'اعتماد'))),
            'archivedQuestions' => count(array_filter($d['questions'], fn ($q) => $st($q) === 'مؤرشف')),
            'publishedExams' => count(array_filter($d['exams'], fn ($e) => $st($e) === 'منشور')),
            'totalExamAttempts' => (int) array_sum(array_map(fn ($e) => self::num($e['attempts'] ?? 0), $d['exams'])),
            'ordersPaid' => count($paid),
            'ordersFailed' => count($failed),
            'revenueSar' => (int) array_sum(array_map(fn ($o) => self::digits($o['amount'] ?? ''), $paid)),
            'aiUsage' => count($d['ai']),
            'downloads' => (int) (array_sum(array_map(fn ($m) => self::num($m['downloads'] ?? 0), $d['media']))
                + array_sum(array_map(fn ($r) => self::num($r['downloads'] ?? 0), $d['resources']))),
        ];
    }

    /** deterministic 14-day series derived from the attempts base */
    public static function activitySeries(int $attemptsSeed, int $days = 14): array
    {
        $base = max(12, (int) round($attemptsSeed / 40));
        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $wave = sin(($days - $i) / 2.1) * 0.18 + cos(($days - $i) / 3.7) * 0.12;
            $drift = 1 + ($days - $i) * 0.012;
            $out[] = ['label' => "د-{$i}", 'value' => max(4, (int) round($base * $drift * (1 + $wave)))];
        }

        return $out;
    }

    public static function planDistribution(array $students): array
    {
        $aliases = ['VIP' => 'باقة ملكية VIP', 'Gold' => 'باقة ذهبية', 'Free' => 'باقة مجانية'];
        $norm = fn ($p) => $aliases[(string) $p] ?? (string) $p;

        return array_map(fn ($l) => [
            'label' => $l,
            'value' => count(array_filter($students, fn ($s) => $norm($s['plan'] ?? '') === $l)),
        ], ['باقة ملكية VIP', 'باقة ذهبية', 'باقة مجانية']);
    }

    public static function skillHeatmap(array $questions): array
    {
        $map = [];
        foreach ($questions as $q) {
            $k = (string) ($q['skill'] ?? 'غير مصنف');
            $map[$k] ??= ['total' => 0, 'published' => 0];
            $map[$k]['total']++;
            if (($q['status'] ?? '') === 'منشور') {
                $map[$k]['published']++;
            }
        }
        $rows = [];
        foreach ($map as $skill => $v) {
            $rows[] = ['skill' => $skill] + $v;
        }
        usort($rows, fn ($a, $b) => $b['total'] <=> $a['total']);

        return array_slice($rows, 0, 8);
    }

    public static function conversionFunnel(array $students, array $orders): array
    {
        $paid = count(array_filter($orders, fn ($o) => ($o['status'] ?? '') === 'مدفوع'));
        $gold = count(array_filter($students, fn ($s) => ($s['plan'] ?? '') !== 'باقة مجانية'));
        $stages = [
            ['label' => 'زوار', 'value' => count($students) * 30],
            ['label' => 'مسجلون', 'value' => count($students)],
            ['label' => 'مستعدون للدفع', 'value' => $gold],
            ['label' => 'مدفوع', 'value' => $paid],
        ];
        foreach ($stages as $i => $s) {
            if ($i > 0) {
                $stages[$i]['value'] = min($s['value'], $stages[$i - 1]['value']);
            }
        }

        return $stages;
    }

    public static function insights(array $k, array $heat): array
    {
        $out = [];
        $push = function (array $i) use (&$out) {
            $out[] = ['id' => 'ins-'.(count($out) + 1)] + $i;
        };
        $fmt = fn ($n) => number_format($n);

        $ratio = $k['activeStudents'] / max(1, $k['activeStudents'] + $k['suspendedStudents']);
        if ($ratio >= 0.8) {
            $push(['scope' => 'نمو', 'severity' => 'إيجابي', 'horizon' => 'شهر', 'title' => 'صحة النشاط قوية',
                'metric' => round($ratio * 100).'% طلاب نشطون',
                'detail' => "{$k['activeStudents']} حسابًا مفعلًا مقابل {$k['suspendedStudents']} معلقًا فقط.",
                'recommendation' => 'اجعل حملة الاسترجاع (Win-back) للطلاب المعلقين أولوية ثانوية وركّز الميزانية على الاستحواذ الجديد.']);
        } else {
            $push(['scope' => 'مخاطرة', 'severity' => 'تحذيري', 'horizon' => 'فوري', 'title' => 'نسبة تعليق مرتفعة',
                'metric' => round((1 - $ratio) * 100).'% معلق',
                'detail' => "{$k['suspendedStudents']} حسابًا معلقًا مقابل {$k['activeStudents']} نشط.",
                'recommendation' => 'أطلق تدفق استرجاع آلي: رسالة واتساب + محاكي مجاني مخصص للطلاب المعلقين هذا الأسبوع.']);
        }

        if ($k['pendingQuestions'] > 0) {
            $load = (int) round($k['pendingQuestions'] / max(1, $k['publishedQuestions'] + $k['pendingQuestions']) * 100);
            $push(['scope' => 'تشغيل', 'severity' => $load > 30 ? 'حرج' : 'محايد', 'horizon' => $load > 30 ? 'فوري' : 'أسبوع',
                'title' => 'طابور مراجعة الأسئلة', 'metric' => "{$k['pendingQuestions']} سؤالًا معلقًا ({$load}%)",
                'detail' => "{$k['publishedQuestions']} سؤالًا منشورًا مقابل {$k['pendingQuestions']} بانتظار المراجعة/الاعتماد.",
                'recommendation' => 'خصص جلستَي مراجعة (١٥ دقيقة × ٢) أسبوعيًا لتصفير الطابور — كل سؤال مؤجل هو جلسة تدريب مفقودة لطالب.']);
        }

        $weak = array_values(array_filter($heat, fn ($h) => $h['total'] > 0));
        usort($weak, fn ($a, $b) => ($a['published'] / $a['total']) <=> ($b['published'] / $b['total']));
        $w = $weak[0] ?? null;
        if ($w && $w['published'] / $w['total'] < 0.5) {
            $push(['scope' => 'تعلم', 'severity' => 'تحذيري', 'horizon' => 'شهر',
                'title' => "فجوة تغطية في «{$w['skill']}»",
                'metric' => "{$w['published']}/{$w['total']} منشور (".round($w['published'] / $w['total'] * 100).'%)',
                'detail' => 'مهارة ذات طلب مرتفع مع نسبة نشر منخفضة — نقطة تحسين أسرع مكسبًا دراسيًا.',
                'recommendation' => "اطلب من فريق المحتوى ٥ أسئلة جديدة منشورة في «{$w['skill']}» خلال أسبوعين مع أولوية نشر عالية."]);
        }

        if ($k['ordersPaid'] + $k['ordersFailed'] > 0) {
            $failRate = (int) round($k['ordersFailed'] / ($k['ordersPaid'] + $k['ordersFailed']) * 100);
            if ($failRate >= 20) {
                $push(['scope' => 'إيراد', 'severity' => 'حرج', 'horizon' => 'فوري', 'title' => 'معدل فشل دفع مرتفع',
                    'metric' => "{$failRate}% من الطلبات فاشلة",
                    'detail' => "{$k['ordersPaid']} مدفوعة مقابل {$k['ordersFailed']} فاشلة — كل نقطة تُنقص الإيراد الشهري.",
                    'recommendation' => 'راجع بوابة الدفع (مدى/ApplePay) وأضف تذكيرًا آليًا لإعادة المحاولة بعد ساعة — عادة يسترد ٣٠–٥٠٪ من الطلبات الفاشلة.']);
            } else {
                $push(['scope' => 'إيراد', 'severity' => 'إيجابي', 'horizon' => 'ربع', 'title' => 'قناة إيراد صحية',
                    'metric' => "{$failRate}% فشل فقط",
                    'detail' => 'الإيراد المحقق حسب البيانات: '.$fmt($k['revenueSar']).' ر.س.',
                    'recommendation' => 'القناة مستقرة — اختبر رفع سعر المسار المتقدم ١٠٪ على الشريحة الجديدة وقس الأثر على التحويل.']);
            }
        }

        if ($k['publishedExams'] === 0) {
            $push(['scope' => 'تشغيل', 'severity' => 'حرج', 'horizon' => 'فوري', 'title' => 'لا توجد نماذج منشورة',
                'metric' => '0 نموذج منشور',
                'detail' => 'المحاكي جاهز تقنيًا لكن بلا نماذج حية — الطالب لا يجد ما يحاكيه.',
                'recommendation' => 'انشر نموذجًا تجريبيًا واحدًا اليوم (٢٥ سؤالًا) لتفعيل حلقة التعلم الكاملة.']);
        } else {
            $perExam = (int) round($k['totalExamAttempts'] / max(1, $k['publishedExams']));
            $push(['scope' => 'نمو', 'severity' => $perExam >= 500 ? 'إيجابي' : 'محايد', 'horizon' => 'شهر',
                'title' => 'عمق استخدام المحاكي', 'metric' => "~{$perExam} محاولة/نموذج",
                'detail' => "{$k['totalExamAttempts']} محاولة موزعة على {$k['publishedExams']} نموذجًا منشورًا.",
                'recommendation' => $perExam >= 500
                    ? 'الاستخدام عميق — أضف نموذجًا متقدمًا (نخبة ٩٥+) لرفع متوسط السعر والاحتفاظ.'
                    : 'اربط المحاكي بالخطة اليومية (١ محاكي قصير/يوم) لرفع التكرار قبل الدورة القادمة.']);
        }

        $push(['scope' => 'تشغيل', 'severity' => $k['aiUsage'] >= 5 ? 'إيجابي' : 'محايد', 'horizon' => 'أسبوع',
            'title' => 'استخدام المعلم الذكي والملفات',
            'metric' => "{$k['aiUsage']} طلب AI · {$k['downloads']} تنزيل",
            'detail' => 'مؤشر تبنٍّ للأدوات الذكية والخزينة التعليمية.',
            'recommendation' => $k['aiUsage'] >= 5
                ? 'التبنّي جيد — وسّع حصة AI لطلاب المسار المتقدم مع سقف يومي مرتفع لقياس الأثر.'
                : 'فعّل تلميحًا داخل بوابة الطالب يدعو لتجربة المعلم الذكي مرة واحدة يوميًا.']);

        return $out;
    }

    public static function summary(array $k, array $insights): string
    {
        $count = fn ($sev) => count(array_filter($insights, fn ($i) => $i['severity'] === $sev));
        $critical = $count('حرج');
        $warn = $count('تحذيري');
        $pos = $count('إيجابي');
        $tone = $critical > 0 ? 'يتطلب تدخلًا فوريًا' : ($warn > 0 ? 'مستقر مع نقاط تحسين' : 'مستقر وأداء إيجابي');
        $firstCritical = collect($insights)->firstWhere('severity', 'حرج');
        $quick = $firstCritical['recommendation'] ?? ($insights[0]['recommendation'] ?? 'لا شيء');

        return "حالة المنصة: {$tone}. {$pos} مؤشرًا إيجابيًا مقابل {$warn} تحذيرًا و{$critical} حالة حرجة — "
            ."{$k['activeStudents']} طالبًا نشطًا · {$k['publishedQuestions']} سؤالًا منشورًا · "
            ."{$k['ordersPaid']} طلبًا مدفوعًا · {$k['totalExamAttempts']} محاولة محاكاة. "
            ."أسرع قرار مقترح: {$quick}";
    }
}
