<?php

namespace App\Http\Controllers\Auth;

use App\Mail\MailModel;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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

        $mailData = [
			'view' => 'recovery.email',
			'subject' => 'Password Recovery',
			'attachments' => [],

            'name' => $user->name,
            'email' => $request->email,
			'token' => 'token-null'
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

	function resetPassword(Request $request, string $token)
	{

	}
}
