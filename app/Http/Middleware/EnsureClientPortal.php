<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientPortal
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->hasAnyRole(['client', 'client-manager'])) {
            abort(403);
        }

        if (! $request->user()->clientProfile) {
            abort(403, 'Contul client nu este asociat unui profil.');
        }

        return $next($request);
    }
}
