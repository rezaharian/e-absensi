<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleRedirect
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            } elseif (Auth::user()->role === 'user') {
                return redirect('/user');
            }
        }


        return $next($request);
    }
}
