<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user has one of the specified roles.
     * Usage: ->middleware('role:admin,owner')
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Allowed roles (e.g., 'admin', 'owner', 'customer')
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException  If user lacks required role
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            abort(401, 'Unauthorized');
        }

        $userRole = $request->user()->role ?? null;

        if (!$userRole || !in_array($userRole, $roles)) {
            abort(403, 'Forbidden: You do not have access to this resource.');
        }

        return $next($request);
    }
}
