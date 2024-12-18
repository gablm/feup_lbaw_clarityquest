@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('login') }}">
	{{ csrf_field() }}

	@if ($errors->has('recover'))
			<span class="auth-error bold">
				{{ $errors->first('recover') }}
			</span>
	@endif

	@if (session('success'))
		<p class="auth-success bold">
			{{ session('success') }}
		</p>
	@endif

	<div class="mb-4">
		<label class="auth" for="email">E-mail</label>
		<input class="auth focus:outline-none focus:shadow-outline" id="email" type="email" name="email"
			value="{{ old('email') }}" required placeholder="Enter your email">
		@if ($errors->has('email'))
			<span class="auth-error bold">
				{{ $errors->first('email') }}
			</span>
		@endif
	</div>

	<div class="mb-4">
		<label class="auth" for="password">Password</label>
		<input class="auth focus:outline-none focus:shadow-outline" id="password" type="password" name="password"
			required placeholder="Enter your password">
		@if ($errors->has('password'))
			<span class="auth-error bold">
				{{ $errors->first('password') }}
			</span>
		@endif
	</div>

	<div class="mb-6">
		<label for="remember">
			<input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
		</label>
	</div>

	<div class="flex items-center justify-between">
		<div class="flex space-x-4 items-center">
			<button class="auth-main focus:outline-none focus:shadow-outline" type="submit">
				Sign In
			</button>
			<a class="auth-link" href="{{ route('register') }}">
				Register
			</a>
		</div>
		<a class="auth-link" href="{{ route('recover.index') }}">
			Forgot password?
		</a>
	</div>
</form>
<h1 class="mt-5 mb-3 font-bold">Login in with external accounts</h1>
<div class="flex items-center justify-between space-x-2">
	@include('partials.google-btn', ['linked' => 0])
	@include('partials.x-btn', ['linked' => 0])
</div>
@endsection