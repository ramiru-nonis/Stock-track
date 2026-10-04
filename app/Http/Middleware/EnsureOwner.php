<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwner
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isOwner()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden. Owner privileges required.'], 403);
            }
            abort(403, 'Forbidden. Owner privileges required.');
        }

        return $next($request);
    }
}
