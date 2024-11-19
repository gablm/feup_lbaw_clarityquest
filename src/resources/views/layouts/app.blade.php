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
    {{-- Navbar --}}
    @include('partials.navbar')

    <main class="grid grid-cols-12 h-screen">
        {{-- Sidebar --}}
        <div class="col-span-3 h-full bg-gray-100 p-4">
            @include('partials.sidebar')
        </div>

        {{-- Main Content --}}
        <div class="col-span-9 p-4">
            <section id="content" class="mt-4">
                @yield('content')
            </section>
        </div>
    </main>
</body>

</html>
