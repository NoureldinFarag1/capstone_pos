<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyDataAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('data_verified')) {
            session(['data_verified' => false]);
        }
        return $next($request);
    }
}
