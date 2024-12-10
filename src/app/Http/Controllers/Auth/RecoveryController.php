<?php

namespace App\Http\Controllers\Auth;

use App\Mail\MailModel;
use App\Models\User;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class RecoveryController extends Controller
{
	function sendRecoverEmail(Request $request) 
	{
		$request->validate([
			'email' => ['required', 'email']
		]);

		$user = User::firstOrFail(['email' => $request->email]);

        $mailData = [
            'name' => $user->name,
            'email' => $request->email,
        ];

        Mail::to($request->email)
			->send(new MailModel($mailData));
        return redirect()->route('home');
    }
}
