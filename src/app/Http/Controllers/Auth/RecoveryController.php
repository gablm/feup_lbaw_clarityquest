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

		$user = User::where('email', $request->email)
			->first();

		if ($user == null)
			return redirect()->route('recover.sent');

		$token = Str::random(128);

		DB::table('passwordresets')->insert([
			'email' => $request->email, 
			'token' => $token
		]);

        $mailData = [
			'view' => 'recovery.email',
			'subject' => 'Password Recovery',
			'attachments' => [],

            'name' => $user->name,
            'email' => $request->email,
			'token' => $token
        ];

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
		
		if ($reset == null)
		{
			return redirect('/login')->withErrors([
				'recover' => 'Invalid recovery token.'
			]);
		}
		
		$creation = strtotime($reset->created_at);
		$now = time();
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

		if ($reset == null)
			return redirect('/login')->withErrors([
					'recover' => 'Invalid recovery token.'
				]);
		
		$creation = strtotime($reset->created_at);
		$now = time();
		if (($now - $creation) / 60 > 15)
			return redirect('/login')->withErrors([
				'recover' => 'Expired recovery token.'
			]);

		User::where('email', $reset->email)
			->update([
				'password' => Hash::make($request->password)
			]);

		DB::table('passwordresets')
			->where([
				'email' => $request->token
			])->delete();

		return redirect('/login')
			->with('success', 'Your password has been changed!');
	}
}
