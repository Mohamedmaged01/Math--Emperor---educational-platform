<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email', 'max:190']], [
            'email.*' => 'اكتب بريدًا إلكترونيًا صالحًا مثل: name@example.com',
        ]);
        NewsletterSubscriber::firstOrCreate(['email' => Str::lower(trim($request->input('email')))]);

        return response()->json(['ok' => true]);
    }
}
