<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(Response::HTTP_FORBIDDEN, 'Akun Anda tidak aktif atau tidak ditemukan.');
        }

        if (! in_array($user->role, ['admin', 'superadmin'], true)) {
            abort(Response::HTTP_FORBIDDEN, 'Akses terbatas untuk Administrator.');
        }

        return $next($request);
    }
}
