<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{{ config('app.name', 'Laravel') }}</title>

	<script type="text/javascript">
		// Fix for Firefox autofocus CSS bug
		// See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
	</script>
	<script type="text/javascript" src={{ url('js/app.js') }} defer>
	</script>
</head>

<a href="{{ url('/') }}">
	<img height="128px" alt="Clarity Quest Logo" src="{{ url('img/logo.png') }}">
</a>
<p>Hello, <span style="font-weight: bold">{{ $mailData['name'] }}</span>!</p>
<p>A password recovery request for the account associated
	with this email (<span style="font-weight: bold">{{ $mailData['email'] }}</span>)
	was requested.<br>Click the link below to continue. This link is valid for 15 minutes.</p>
<a href="{{ url('/recover/' . $mailData['token']) }}">
	Recover password
</a>
<p style="font-weight: bold">If this email was not requested by you, please ignore it.</p>



</html>