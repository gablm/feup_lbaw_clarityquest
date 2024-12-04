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
		$actualUser = Auth::user();
		if ($actualUser && $actualUser->google_token)
		{
			$actualUser->google_token = null;
			$actualUser->save(); 

			return redirect()->route('profile.edit');
		}

		return Socialite::driver('google')->redirect();
	}

	public function handleGoogleCallback(Request $request)
	{	
		$user = Socialite::driver('google')->user();
		$actualUser = User::where('google_token', $user->id)->first();
		
		if ($actualUser && Auth::check() == false) {

			Auth::login($actualUser);
			$request->session()->regenerate();

			return redirect()->intended('/');
		}

		if (Auth::check()) {
			$actualUser = Auth::user();
			$actualUser->google_token = $user->id;
			$actualUser->save(); 

			return redirect()->route('profile.edit');
		}

		return redirect()->route('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
	}

	public function redirectToX()
	{
		$actualUser = Auth::user();
		if ($actualUser && $actualUser->x_token)
		{
			$actualUser->x_token = null;
			$actualUser->save(); 

			return redirect()->route('profile.edit');
		}

		return Socialite::driver('twitter')->redirect();
	}

	public function handleXCallback(Request $request)
	{	
		$user = Socialite::driver('twitter')->user();
		$actualUser = User::where('x_token', $user->id)->first();
		
		if ($actualUser && Auth::check() == false) {

			Auth::login($actualUser);
			$request->session()->regenerate();

			return redirect()->intended('/');
		}

		if (Auth::check()) {
			$actualUser = Auth::user();
			$actualUser->x_token = $user->id;
			$actualUser->save(); 

			return redirect()->route('profile.edit');
		}

		return redirect()->route('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
	}
}
