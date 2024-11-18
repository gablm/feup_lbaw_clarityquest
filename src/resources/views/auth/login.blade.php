@extends('layouts.auth')

@section('content')
<div class="min-w-[35vw]">
	<form method="POST" action="{{ route('login') }}">
		{{ csrf_field() }}

		<div class="mb-4">
			<label class="block text-gray-700 text-sm font-bold mb-2" for="email">E-mail</label>
			<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" name="email" value="{{ old('email') }}" required>
			@if ($errors->has('email'))
			<span class="text-red-500 text-xs bold">
				{{ $errors->first('email') }}
			</span>
			@endif
		</div>

		<div class="mb-4">
			<label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
			<input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" name="password" required>
			@if ($errors->has('password'))
			<span class="text-red-500 text-xs bold">
				{{ $errors->first('password') }}
			</span>
			@endif
		</div>

		<div class="mb-6">
			<label class="appearance-none rounded text-gray-700 leading-tight focus:outline-none">
				<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
			</label>
		</div>

		<div class="flex items-center justify-between">
			<button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
				Sign In
			</button>
			<a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="{{ route('register') }}">
				Register
			</a>
		</div>
		@if (session('success'))
		<p class="success">
			{{ session('success') }}
		</p>
		@endif
	</form>
</div>
@endsection