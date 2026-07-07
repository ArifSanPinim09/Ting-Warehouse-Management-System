<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * PRD §7.5: User Akses URL → Sudah Login? → Akun Aktif?
     * If not active: Logout + Redirect /login + Error
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->status !== User::STATUS_ACTIVE) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // PRD §13.1 messages
            if ($user->status === User::STATUS_PENDING) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun belum aktif. Hubungi admin.',
                ]);
            }

            if ($user->status === User::STATUS_INACTIVE) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Akun telah dinonaktifkan.',
                ]);
            }
        }

        return $next($request);
    }
}
