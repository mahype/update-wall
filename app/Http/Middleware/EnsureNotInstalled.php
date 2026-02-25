<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            if (\App\Models\User::count() > 0) {
                return redirect()->route('login');
            }
        } catch (\Exception) {
            // DB not yet migrated → let installer through
        }

        return $next($request);
    }
}
