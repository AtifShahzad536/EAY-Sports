<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfUserAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If Admin is authenticated, redirect to Admin Dashboard
        if (auth('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        // 2. If standard Auth (Customer or Dealer) is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'dealer') {
                return redirect()->route('home');
            }

            return redirect()->route('home');
        }

        return $next($request);
    }
}
