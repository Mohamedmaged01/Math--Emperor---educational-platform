<?php

namespace App\Http\Controllers;

use App\Support\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SimulatorController extends Controller
{
    /**
     * POST /simulator/attempts — the landing simulator reports the chosen
     * options; the score is computed here (not trusted from the client) and
     * stored in the signed-in student's portal.
     */
    public function store(Request $request): JsonResponse
    {
        $questions = Content::quizQuestions();
        $data = $request->validate([
            'answers' => ['required', 'array', 'size:'.count($questions)],
            'answers.*' => ['integer', 'between:-1,3'],
        ]);

        $user = $request->user();
        if (! $user || ! $user->isStudent()) {
            return response()->json(['recorded' => false]);
        }

        $correct = 0;
        foreach ($questions as $i => $q) {
            if ((int) $data['answers'][$i] === $q['answer']) {
                $correct++;
            }
        }
        $total = count($questions);

        $user->simRecords()->create([
            'label' => 'اختبار قصير — تجربة المحاكي (٣ أسئلة)',
            'score' => (int) round($correct / $total * 100),
            'correct' => $correct,
            'total' => $total,
            'taken_on' => now()->toDateString(),
        ]);
        $user->increment('streak');
        $user->increment('points', $correct * 25);

        // keep at most 30 records per student (original portal cap)
        $keep = $user->simRecords()->limit(30)->pluck('id');
        $user->simRecords()->whereNotIn('id', $keep)->delete();

        return response()->json(['recorded' => true, 'correct' => $correct, 'total' => $total]);
    }
}
