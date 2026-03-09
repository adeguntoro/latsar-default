<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSingleSession
{
    /**
     * Handle an incoming request.
     * Ensures only one active session per user by invalidating older sessions.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $sessionToken = $request->session()->get('session_token');
            
            // If user has a session_token in DB but session doesn't have one, sync it
            if (!$sessionToken && $user->session_token) {
                $request->session()->put('session_token', $user->session_token);
            }
            
            // If session has token but it doesn't match DB, logout (session from another device)
            if ($sessionToken && $user->session_token && $sessionToken !== $user->session_token) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Your session has been terminated due to login from another device.');
            }
        }
        
        return $next($request);
    }
}