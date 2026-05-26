<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Records last_login_at / last_login_ip on each authenticated web request.
 * Uses a session flag so the DB write happens once per session, not per request.
 */
class TrackLastLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check()
            && ! $request->session()->has('login_tracked')
        ) {
            auth()->user()->recordLogin($request->ip());
            $request->session()->put('login_tracked', true);
        }

        return $next($request);
    }
}
