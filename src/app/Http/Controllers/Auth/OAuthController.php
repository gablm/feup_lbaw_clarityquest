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
		// If the user already has a token, remove it and redirect back.
		if ($actualUser && $actualUser->google_token)
		{
			$actualUser->google_token = null;
			$actualUser->save();

			return redirect()->route('profile.edit');
		}

		// Redirect to OAuth Provider
		return Socialite::driver('google')->redirect();
	}

	public function handleGoogleCallback(Request $request)
	{	
		$user = Socialite::driver('google')->user();
		$actualUser = User::where('google_token', $user->id)->first();
		
		// Check if a user with the token exists and if there is not account logged in
		if ($actualUser && Auth::check() == false)
		{
			// Check if the user is blocked
			if ($actualUser->isBlocked())
				return back()->withErrors([
					'email' => 'The provided account is blocked.',
				])->onlyInput('email');

			Auth::login($actualUser);
			$request->session()->regenerate();

			return redirect()->intended('/');
		}

		// Check if the user if logged in and is linking the account
		if (Auth::check())
		{
			$curr = Auth::user();
			
			// Check if the account already is connected to other account
			if ($actualUser && $actualUser->id != $curr->id)
			{
				return redirect()->route('profile.edit')
					->withErrors([
						'connection' => 'This Google account is already connected to other account.'
					]);
			}

			$curr->google_token = $user->id;
			$curr->save(); 

			return redirect()->route('profile.edit');
		}

		return redirect()->route('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
	}

	public function redirectToX()
	{
		$actualUser = Auth::user();
		// If the user already has a token, remove it and redirect back.
		if ($actualUser && $actualUser->x_token)
		{
			$actualUser->x_token = null;
			$actualUser->save(); 

			return redirect()->route('profile.edit');
		}

		// Redirect to OAuth Provider
		return Socialite::driver('twitter')->redirect();
	}

	public function handleXCallback(Request $request)
	{	
		$user = Socialite::driver('twitter')->user();
		$actualUser = User::where('x_token', $user->id)->first();
		
		// Check if a user with the token exists and if there is not account logged in
		if ($actualUser && Auth::check() == false)
		{
			if ($actualUser->isBlocked())
				return back()->withErrors([
					'email' => 'The provided account is blocked.',
				])->onlyInput('email');

			Auth::login($actualUser);
			$request->session()->regenerate();

			return redirect()->intended('/');
		}

		// Check if the user if logged in and is linking the account
		if (Auth::check())
		{
			$curr = Auth::user();
			
			// Check if the account already is connected to other account
			if ($actualUser && $actualUser->id != $curr->id)
			{
				return redirect()->route('profile.edit')
					->withErrors([
						'connection' => 'This X account is already connected to other account.'
					]);
			}

			$curr->x_token = $user->id;
			$curr->save(); 

			return redirect()->route('profile.edit');
		}

		return redirect()->route('login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
	}
}
