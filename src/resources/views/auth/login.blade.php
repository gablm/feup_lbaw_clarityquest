@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('login') }}">
    {{ csrf_field() }}
    
    @if (session('success'))
    <p class="auth-success bold">
        {{ session('success') }}
    </p>
    @endif

    <div class="mb-4">
        <label class="auth" for="email">E-mail</label>
        <input class="auth focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="Enter your email">
        @if ($errors->has('email'))
        <span class="auth-error bold">
            {{ $errors->first('email') }}
        </span>
        @endif
    </div>

    <div class="mb-4">
        <label class="auth" for="password">Password</label>
        <input class="auth focus:outline-none focus:shadow-outline" id="password" type="password" name="password" required placeholder="Enter your password">
        @if ($errors->has('password'))
        <span class="auth-error bold">
            {{ $errors->first('password') }}
        </span>
        @endif
    </div>

    <div class="mb-6">
        <label>
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
        </label>
    </div>

    <div class="flex items-center justify-between">
        <button class="auth-main focus:outline-none focus:shadow-outline" type="submit">
            Sign In
        </button>
        <a class="auth-link" href="{{ route('register') }}">
            Register
        </a>
    </div>
</form>
@endsection