<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->guest('/login');
        }

        $userRole = Auth::user()->role;

        $allowedRoles = count($roles) ? $roles : config('roles', []);

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
