<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsParent
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->students()->exists()) {
            abort(403, 'ليس لديك صلاحية الوصول كولي أمر.');
        }

        return $next($request);
    }
}
