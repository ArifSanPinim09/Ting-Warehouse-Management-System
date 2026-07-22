<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sprint 3: Check if customer is blacklisted.
 * If blacklisted, block access and redirect with error message.
 */
class CheckBlacklist
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->is_blacklisted) {
            // Allow logout
            if ($request->routeIs('logout')) {
                return $next($request);
            }

            // If API or Livewire, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akun Anda di-blacklist: ' . ($user->blacklist_reason ?? 'Hubungi admin.'),
                ], 403);
            }

            // Redirect to error page
            abort(403, 'Akun Anda di-blacklist: ' . ($user->blacklist_reason ?? 'Hubungi admin.'));
        }

        return $next($request);
    }
}
