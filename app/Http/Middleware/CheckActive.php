<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActive
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::check() && !Auth::user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        return $next($request);
    }
}
