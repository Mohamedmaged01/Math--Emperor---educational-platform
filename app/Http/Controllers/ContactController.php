<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Support\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'subject' => ['required', Rule::in(Content::contactSubjects())],
            'message' => ['required', 'string', 'max:5000'],
        ]);
        ContactMessage::create($data);

        return response()->json([
            'ok' => true,
            'team' => $data['subject'] === 'شراكة مدرسية' ? 'الشراكات المؤسسية' : 'الدعم التعليمي',
        ]);
    }
}
