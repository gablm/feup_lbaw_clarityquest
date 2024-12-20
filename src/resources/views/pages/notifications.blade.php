@extends('layouts.app')

@section('title', 'Notifications')

@php
    $crumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Notifications', 'url' => route('pages.notifications')]
    ];
@endphp

@section('content')
<div class="flex flex-row flex-grow">
	@include('partials.sidebar')

	<div class="container mx-auto p-4">
		{!! breadcrumbs($crumbs) !!}
		<h2 class="text-2xl font-semibold mb-4">
			Notifications
			@include('partials.tip', ['tip' => "Here you can see your notifications. To delete a notification, click on the cross icon."])
		</h2>
		@forelse ($notifications as $notification)
		@include('partials.notification-card', ['notification' => $notification])
		@empty
		<div class="text-center text-gray-500">
			<p>No notifications found.</p>
		</div>
		@endforelse
	</div>
</div>
@endsection
