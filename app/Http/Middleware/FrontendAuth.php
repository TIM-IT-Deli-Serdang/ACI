<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class FrontendAuth
{
    public function handle($request, Closure $next)
    {
        // Jika tidak ada token/user -> redirect login
        if (!Session::has('auth_token') || !Session::has('user')) {

            // Kalau permintaan AJAX / Fetch API
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
