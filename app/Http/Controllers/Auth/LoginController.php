<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // default
    // protected $redirectTo = '/home';
    protected function redirectTo()
    {
        $role = auth()->user()->getRoleNames()->first();

        return '/'.$role.'/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle successful authentication - Invalidate other device sessions
     * 
     * When a user logs in from a new device, this method:
     * 1. Generates a new session_token for the user
     * 2. Stores the token in the session
     * 3. Regenerates the session ID
     * 
     * The EnsureSingleSession middleware (added to bootstrap/app.php) will:
     * - Check the session_token on every request
     * - Automatically log out users from other devices that have the old token
     * - Ensure only the most recent login session remains active
     */
    protected function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // OLD CODE: No session token management
        // return redirect()->intended($this->redirectPath());
        
        // NEW CODE: Regenerate session token to invalidate other sessions
        $newToken = Str::random(255);
        $user->update([
            'session_token' => $newToken,
        ]);
        
        // Store token in session for comparison on subsequent requests
        $request->session()->put('session_token', $newToken);
        
        // Regenerate session ID for additional security
        $request->session()->regenerate();
        
        return redirect()->intended($this->redirectPath());
    }
}