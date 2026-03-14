<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     * Ensures the authenticated user has at least one role.
     * If not, redirects to 403 error page.
     */
    public function handle(Request $request, Closure $next)
    {
if (Auth::check()) {
    $user = Auth::user();
    
    // Check if user has any role
            if ($user && !$user->roles()->exists()) {
                return redirect()->route('no.role');
            }
        }
        
        return $next($request);
    }
}