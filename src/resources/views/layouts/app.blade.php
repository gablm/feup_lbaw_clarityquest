<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>{{ config('app.name', 'Laravel') }}</title>
	<link rel="icon" type="image/x-icon" href={{ url('favicon.ico') }}>

	<!-- Styles -->
	@vite('resources/css/app.css')

	<script type="text/javascript">
		// Fix for Firefox autofocus CSS bug
		// See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
	</script>
	<script type="text/javascript" src={{ url('js/app.js') }} defer>
	</script>
</head>

<body>
	<main class="grid h-screen place-items-center">
		<div class="flex flex-col items-center">
			<a href="{{ url('/') }}">
				<img class="h-32 md:h-48 lg:h-64" src={{ url('img/logo.png') }}>
			</a>
			<section id="auth-bar-temp" class="flex flex-col items-center min-w-[40vw] mt-5 p-[1em] shadow-lg border border-gray-200 rounded">
				@if (Auth::check())
				<span>Logged in as {{ Auth::user()->name }} ({{ Auth::user()->email }})!</span>
				<a href="{{ url('/logout') }}">
					<button class="auth-main">Logout</button>
				</a>
				@else
				<a href="{{ url('/login') }}">
					<button class="auth-main">Login</button>
				</a>
				@endif
			</section>

			<section id="content" class="min-w-[40vw] mt-5 p-[1em] shadow-lg border border-gray-200 rounded">
				@yield('content')
			</section>
		</div>
	</main>
</body>

</html>