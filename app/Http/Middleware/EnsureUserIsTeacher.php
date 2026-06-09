<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsTeacher
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // غير مسجّل الدخول: نترك Authenticate يعالج التوجيه لصفحة الدخول.
        if ($user && ! $user->teacher()->exists()) {
            abort(403, 'ليس لديك صلاحية الوصول كمعلم.');
        }

        return $next($request);
    }
}
