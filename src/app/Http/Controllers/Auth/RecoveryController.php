<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Mail\MailModel;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Http\Controllers\Controller;

class RecoveryController extends Controller
{
	function sendEmail(Request $request) 
	{
		$request->validate([
			'email' => ['required', 'email']
		]);

		// Find user that has the provided email
		$user = User::where('email', $request->email)
			->first();

		// If the user does not exist, pretend the email was succesfully sent.
		if ($user == null)
			return redirect()->route('recover.sent');

		// Generate a new reset token
		$token = Str::random(128);

		// Add token to database
		DB::table('passwordresets')->insert([
			'email' => $request->email, 
			'token' => $token
		]);

		// Fill email data with required parameters
        $mailData = [
			'view' => 'recovery.email',
			'subject' => 'Password Recovery',
			'attachments' => [],

            'name' => $user->name,
            'email' => $request->email,
			'token' => $token
        ];

		// Send the email
        Mail::to($request->email)
			->send(new MailModel($mailData));
		
		return redirect()->route('recover.sent');
    }

	function index(Request $request)
	{
		if (Auth::check())
			return redirect()->route(RouteServiceProvider::HOME);

		return view('recovery.index');
	}

	function sent(Request $request)
	{
		return view('recovery.sent');
	}

	function showResetPasswordForm(Request $request, string $token)
	{
		$reset = DB::table('passwordresets')
			->where([
				'token' => $request->token
			])->first();
		
		// Check if there is a row in the resets table that contains the token
		if ($reset == null)
		{
			return redirect('/login')->withErrors([
				'recover' => 'Invalid recovery token.'
			]);
		}
		
		$creation = strtotime($reset->created_at);
		$now = time();

		// Check if the token has not expired (15 minutes have not elapsed)
		if (($now - $creation) / 60 > 15)
			return redirect('/login')->withErrors([
				'recover' => 'Expired recovery token.'
			]);

		return view('recovery.form', [
			'token' => $token
		]);
	}

	function resetPassword(Request $request)
	{
		$request->validate([
			'token' => 'required|string|min:128|max:128',
			'password' => 'required|min:8|confirmed',
		]);

		$reset = DB::table('passwordresets')
			->where([
				'token' => $request->token
			])->first();

		// Check if there is a row in the resets table that contains the token
		if ($reset == null)
			return redirect('/login')->withErrors([
					'recover' => 'Invalid recovery token.'
				]);
		
		$creation = strtotime($reset->created_at);
		$now = time();

		// Check if the token has not expired (15 minutes have not elapsed)
		if (($now - $creation) / 60 > 15)
			return redirect('/login')->withErrors([
				'recover' => 'Expired recovery token.'
			]);

		// Update user password
		User::where('email', $reset->email)
			->update([
				'password' => Hash::make($request->password)
			]);

		// Remove row from reset table
		DB::table('passwordresets')
			->where([
				'email' => $request->token
			])->delete();

		return redirect('/login')
			->with('success', 'Your password has been changed!');
	}
}
