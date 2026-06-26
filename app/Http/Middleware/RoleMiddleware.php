<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Usage: middleware('role:admin') or middleware('role:student')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard');
            }

            return redirect()->route('student.dashboard');
        }

        return $next($request);
    }
}
