<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Signs banned members out on their next request, and refuses API calls made with their tokens.
 */
class EnsureUserIsNotBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user?->isBanned()) {
            return $next($request);
        }

        if ($request->is('api/*') || $request->expectsJson()) {
            abort(403, 'This account has been suspended.');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors(['username' => 'This account has been suspended.']);
    }
}
