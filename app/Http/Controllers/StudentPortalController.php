<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $records = $user->simRecords()->limit(30)->get();

        return view('student.portal', [
            'user' => $user,
            'records' => $records,
            'avg' => $records->count() ? (int) round($records->avg('score')) : 0,
            'collections' => [
                ['name' => 'تجميعة القدرات — الدورة الأخيرة', 'total' => 120, 'done' => 104, 'tone' => 'primary'],
                ['name' => 'تجميعة التحصيلي — فيزياء', 'total' => 80, 'done' => 61, 'tone' => 'secondary'],
                ['name' => 'تجميعة التحصيلي — رياضيات', 'total' => 95, 'done' => 95, 'tone' => 'accent'],
                ['name' => 'تجميعة المصطلح اللفظي', 'total' => 60, 'done' => 22, 'tone' => 'primary'],
            ],
            'weekly' => [62, 78, 55, 84, 91, 71, 96],
        ]);
    }
}
