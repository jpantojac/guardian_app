<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Check if user is active
        if (!$user->is_active) {
            \Illuminate\Support\Facades\Auth::logout();
            return redirect('/login')->with('error', 'Tu cuenta ha sido desactivada.');
        }

        // Check if user has any of the required roles
        if (!in_array($user->role, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta área.');
        }

        return $next($request);
    }
}
