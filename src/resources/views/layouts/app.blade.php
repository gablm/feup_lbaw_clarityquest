<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', config('app.name', 'Laravel'))</title>
	<link rel="icon" type="image/x-icon" href={{ url('favicon.ico') }}>

	<meta property="og:title" content="@yield('title', config('app.name', 'Laravel'))" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="{{ url()->current() }}" />
	<meta property="og:image" content="{{ url('favicon.ico') }}" />

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
		<section id="content" class="z-1 pt-20 pb-5 flex flex-grow">
			@yield('content')
		</section>
		<div id="info-modal" class="hidden modal modal-style">
			<div class="modal-overlay modal-bg"></div>
			<div class="modal-container modal-cont">
				<div class="modal-content py-4 text-left px-6">
					<div class="mb-4">
						<p id="info-text">????</p>
					</div>
					<div class="mt-4 flex space-x-2 justify-end">
						<button class="nav-main" onclick="closeInfoModal()">Close</button>
					</div>
				</div>
			</div>
		</div>
		<div id="delete-modal" class="hidden modal modal-style">
			<div class="modal-overlay modal-bg"></div>
			<div class="modal-container modal-cont">
				<div class="modal-content py-4 text-left px-6">
					<p id="delete-title" class="text-2xl font-bold mb-4">????</p>
					<div class="flex flex-col mb-4">
						<p id="delete-desc">???</p>
						<span class="err hidden auth-error bold mt-1"></span>
					</div>
					<div class="mt-4 flex space-x-2 justify-end">
						<button class="modal-close tool-link" onclick="closeDeleteModal()">Cancel</button>
						<button class="nav-warn" onclick="sendDeleteRequest()">Delete</button>
					</div>
				</div>
			</div>
		</div>
		@include('layouts.footer')
	</main>
</body>

</html>