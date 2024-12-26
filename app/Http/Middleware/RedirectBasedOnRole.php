<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated using Auth facade
        if (Auth::hasUser()) {
            $user = $request->user();

            // Only redirect if accessing the root URL or dashboard
            if ($request->is('/') || $request->is('dashboard')) {
                if ($user->hasRole('cashier')) {
                    return redirect()->route('sales.index');
                }
            }
        }

        return $next($request);
    }
}
