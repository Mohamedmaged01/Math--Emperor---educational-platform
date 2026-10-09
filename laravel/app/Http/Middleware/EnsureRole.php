<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * role:STUDENT / role:STAFF — guests are sent home with the matching login
 * modal opened; a signed-in user of the other role lands on their own portal.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'انتهت الجلسة — سجّل الدخول مجددًا.'], 401);
            }

            return redirect('/')->with('open', $role === 'STAFF' ? 'imperial' : 'student');
        }

        if ($user->role !== $role) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'غير مصرح.'], 403);
            }

            return redirect($user->isStaff() ? route('imperial.show') : route('student.portal'));
        }

        return $next($request);
    }
}
