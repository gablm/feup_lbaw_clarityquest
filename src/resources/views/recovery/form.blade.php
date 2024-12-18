@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('recover.action') }}">
    {{ csrf_field() }}

    <input class="hidden" name="token" value="{{ $token }}">

    <fieldset>
        <legend class="text-2xl font-semibold mb-4">Password Reset</legend>

        <div class="mb-4">
            <label class="auth" for="password">New Password</label>
            <input class="auth focus:outline-none focus:shadow-outline" id="password" type="password" name="password" required placeholder="Enter your password">
            @if ($errors->has('password'))
            <span class="auth-error bold">
                {{ $errors->first('password') }}
            </span>
            @endif
        </div>

        <div class="mb-4">
            <label class="auth" for="password-confirm">Confirm New Password</label>
            <input class="auth focus:outline-none focus:shadow-outline" id="password-confirm" type="password" name="password_confirmation" required placeholder="Confirm your password">
        </div>
    </fieldset>

    <div class="flex items-center space-x-4">
        <button aria-label="Confirm Change Password" class="auth-main focus:outline-none focus:shadow-outline" type="submit">
            Change Password
        </button>
    </div>
</form>
@endsection