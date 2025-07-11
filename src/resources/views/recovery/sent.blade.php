@extends('layouts.auth')

@section('title', 'Recovery - Email Sent')

@section('content')
<form method="POST" action="{{ route('recover.send') }}">
	{{ csrf_field() }}

	@if (session('success'))
		<p class="auth-success bold">
			{{ session('success') }}
		</p>
	@endif

	<fieldset>
		<legend class="text-2xl font-semibold mb-4">Recovery</legend>

		<div class="text-green-500 my-2 flex items-center space-x-4">
			<i class="fa-solid fa-check"></i>
			<span>Recovery email sent successfully!</span>
		</div>

		<div class="my-4">
			<p>Please check your email. If you haven't received</p>
			<p>anything within 5-10 minutes, please try again.</p>
		</div>
	</fieldset>

	<div class="mt-2 flex items-center space-x-4">
		<a class="auth-main" href="{{ route('login') }}">
			Login
		</a>
	</div>
</form>
@endsection