@extends('layouts.auth')

@section('content')
<div class="flex flex-col justify-center space-y-4 max-md:max-w-[50vw] max-w-[30vw]">
	<p>Hello, <span class="font-bold">{{ $mailData['name'] }}</span>!</p>
	<p>A password recovery request for the account associated
		with this email (<span class="font-bold">{{ $mailData['email'] }}</span>)
		was requested.</p>
	<p>Click the link below to continue.</p>
	<a class="auth-link" href="{{ url('/recover/' . $mailData['token']) }}">
		Recover password
	</a>
	<p class="font-bold">If this email was not requested by you, please ignore it.</p>
</div>
@endsection