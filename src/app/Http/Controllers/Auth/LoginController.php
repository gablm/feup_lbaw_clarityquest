<?php
 
namespace App\Http\Controllers\Auth;

//use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

use Illuminate\View\View;

class LoginController extends Controller
{

    /**
     * Display a login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/');
        } else {
            return view('auth.login');
        }
    }

    /**
     * Handle an authentication attempt.
     */
    public function authenticate(Request $request): RedirectResponse
    {
		// Validate rquest credentials
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
 
		// Attempt to login using such credentials
        if (Auth::attempt($credentials, $request->filled('remember')))
		{
			// Check if the user is blocked
			if (Auth::user()->isBlocked())
			{
				// Logout and invalidate session
				Auth::logout();
        		$request->session()->invalidate();
        		$request->session()->regenerateToken();
				return back()->withErrors([
					'email' => 'The provided account is blocked.',
				])->onlyInput('email');
			}
 
			$request->session()->regenerate();
            return redirect()->intended('/');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log out the user from application.
     */
    public function logout(Request $request)
    {
		// Logout and invalidate session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(RouteServiceProvider::HOME)
            ->withSuccess('You have logged out successfully!');
    } 
}
