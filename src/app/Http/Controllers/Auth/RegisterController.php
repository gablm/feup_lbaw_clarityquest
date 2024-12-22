<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\View\View;

use App\Models\User;
use App\Models\Medals;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Display a login form.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
		// Validate the register request according to the requirements
        $request->validate([
            'username' => [
                'required',
                'string',
                'max:32',
                'unique:users',
                'regex:/^[a-zA-Z0-9._-]+$/', 
            ],
            'name' => [
                'required',
                'string',
                'max:250',
                'regex:/^[a-zA-Z\s]+$/', 
            ],
            'email' => [
                'required',
                'email',
                'max:250',
                'unique:users',
                'regex:/^[a-z0-9._-]+@[a-z0-9_.-]+\.[a-z]{2,}$/'
            ],
            'password' => 'required|min:8|confirmed'
        ]);

		// Create User and Medals
        DB::transaction(function () use ($request) {
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            Medals::create(['user_id' => $user->id]);
        });

		// Login and regenerate session
        $credentials = $request->only('email', 'password');
        Auth::attempt($credentials);
        $request->session()->regenerate();
        return redirect("/")
            ->withSuccess('You have successfully registered & logged in!');
    }
}
