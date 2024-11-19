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

	<!-- FontAwesome Icons -->
	<script src="https://kit.fontawesome.com/f23eb02d6c.js" crossorigin="anonymous"></script>

	<script type="text/javascript">
		// Fix for Firefox autofocus CSS bug
		// See: http://stackoverflow.com/questions/18943276/html-5-autofocus-messes-up-css-loading/18945951#18945951
	</script>
	<script type="text/javascript" src={{ url('js/app.js') }} defer>
	</script>
</head>

<body>
	<main class="flex flex-col h-screen justify-between">
    	@include('layouts.navbar')
		<section id="content">
        	@yield('content')
    	</section>
		@include('layouts.footer')
	</main>
</body>

</html>
