<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\Contracts\HasAbilities;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdminApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Valid API token is required.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Must authenticate with token, not session cookie
        if (! ($user->currentAccessToken() instanceof HasAbilities)) {
            return response()->json([
                'success' => false,
                'message' => 'Automation endpoints must be accessed using a Bearer API token, not a session cookie.',
            ], Response::HTTP_FORBIDDEN);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'User account is inactive.',
            ], Response::HTTP_FORBIDDEN);
        }

        if ($user->role !== 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Superadmin role required.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
