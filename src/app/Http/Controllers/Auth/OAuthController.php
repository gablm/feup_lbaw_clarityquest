<?php

namespace App\Http\Controllers\Auth;


use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\User;

class OAuthController extends Controller
{
	public function redirectToGoogle()
	{
		return Socialite::driver('google')->redirect();
	}

	public function handleGoogleCallback(Request $request)
	{
		$user = Socialite::driver('google')->user();
		$actualUser = User::where('google_token', $user->id)->first();

		if ($actualUser) {

			Auth::login($actualUser);
			$request->session()->regenerate();

			return redirect()->intended('/');
		}
		
		if (Auth::check()) {
			$actualUser = Auth::user();
			$actualUser->google_token = $user->id;
			$actualUser->save(); 

			return redirect()->route('profile');
		}

		return redirect()->route('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
	}
}
