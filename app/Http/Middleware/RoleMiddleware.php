<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $allowedRoles = [];

        foreach ($roles as $roleParameter) {
            foreach (explode(',', $roleParameter) as $role) {
                $allowedRoles[] = trim($role);
            }
        }

        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login');
        }

        if (! $user || ! in_array($user->role, $allowedRoles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
