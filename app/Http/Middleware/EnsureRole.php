<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$slugs): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403, 'Access denied.');
        }

        if (! $user->hasAnyRole(...$slugs)) {
            abort(403, 'You do not have access to this resource.');
        }

        return $next($request);
    }
}
