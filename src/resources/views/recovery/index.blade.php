@extends('layouts.auth')

@section('content')
<form method="POST" action="{{ route('recover.send') }}">
	{{ csrf_field() }}

	@if (session('success'))
		<p class="auth-success bold">
			{{ session('success') }}
		</p>
	@endif

	<fieldset>
		<legend class="text-2xl font-semibold mb-4">Recovery Information</legend>

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
	</fieldset>

	<div class="flex items-center space-x-4">
        <button aria-label="Send Recovery Email" class="auth-main focus:outline-none focus:shadow-outline" type="submit">
            Send Recovery Email
        </button>
        <a class="auth-link" href="{{ route('login') }}">
            Login
        </a>
    </div>
</form>
@endsection