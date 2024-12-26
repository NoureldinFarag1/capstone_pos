<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckCashDrawer
{
    public function handle($request, Closure $next)
    {
        // Check if the user is a cashier
        if (Auth::check() && Auth::user()->role === 'cashier') {
            // Check if the user has entered the cash drawer amount for today
            $today = now()->toDateString();
            $hasEnteredCash = \App\Models\CashDrawer::where('user_id', Auth::id())
                ->whereDate('created_at', $today)
                ->exists();

            if (!$hasEnteredCash) {
                // Redirect to the cash drawer entry page if not entered
                return redirect()->route('cashdrawer.create');
            }
        }

        return $next($request);
    }
}
