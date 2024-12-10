@extends('layouts.auth')

@section('content')
<div class="flex flex-col justify-center space-y-4">
	<p>Hello, {{ $mailData['name'] }}!</p>
	<p>A password recovery request for the account associated with this email ({{ $mailData['email'] }}) was requested.</p>
	<p>Click the button below to proceed!</p>
	<p>If this email was not requested by you, please ignore it.</p>
	<a class="auth-link" href="{{ url('/recover/' . $mailData['token']) }}">
		Recover password
	</a>
</div>
@endsection