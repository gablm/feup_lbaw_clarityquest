@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('register') }}">
	{{ csrf_field() }}

	<div class="mb-4">
		<label class="auth" for="name">Name</label>
		<input class="auth focus:outline-none focus:shadow-outline" id="name" type="text" name="name" value="{{ old('name') }}" required>
		@if ($errors->has('name'))
		<span class="auth-error bold">
			{{ $errors->first('name') }}
		</span>
		@endif
	</div>

	<div class="mb-4">
		<label class="auth" for="username">Handle</label>
		<input class="auth focus:outline-none focus:shadow-outline" id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
		@if ($errors->has('username'))
		<span class="auth-error bold">
			{{ $errors->first('username') }}
		</span>
		@endif
	</div>

	<div class="mb-4">
		<label class="auth" for="email">E-Mail Address</label>
		<input class="auth focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="{{ old('email') }}" required>
		@if ($errors->has('email'))
		<span class="auth-error bold">
			{{ $errors->first('email') }}
		</span>
		@endif
	</div>

	<div class="mb-4">
		<label class="auth" for="password">Password</label>
		<input class="auth focus:outline-none focus:shadow-outline" id="password" type="password" name="password" required>
		@if ($errors->has('password'))
		<span class="auth-error bold">
			{{ $errors->first('password') }}
		</span>
		@endif
	</div>

	<div class="mb-4">
		<label class="auth" for="password-confirm">Confirm Password</label>
		<input class="auth focus:outline-none focus:shadow-outline" id="password-confirm" type="password" name="password_confirmation" required>
	</div>

	<div class="flex items-center justify-between">
		<button class="auth-main focus:outline-none focus:shadow-outline" type="submit">
			Register
		</button>
		<a class="auth-link" href="{{ route('login') }}">
			Login
		</a>
	</div>
</form>
@endsection