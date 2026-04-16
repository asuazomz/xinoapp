<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login_1');
        }
        /*dd(Auth::user()->role, $role);*/
        if (Auth::user()->role !== $role) {
            return redirect('/dashboard')->with('error', 'No tienes permiso para entrar ahí');
        }

        return $next($request);
    }
}