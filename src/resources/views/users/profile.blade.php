@extends('layouts.app')

@section('title', "Profile - {$user->name}")

@php
    $crumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Profile', 'url' => route('profile')]
    ];
@endphp

@section('content')
<section class="container mx-auto p-6" onload="showProfileTab('questions')">
	{!! breadcrumbs($crumbs) !!}
	<div class="p-8">
		<div class="flex items-center justify-between mb-8">
			<div class="flex items-center space-x-6">
				<img
					src="{{ $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png') }}"
					alt="Profile Picture"
					class="h-24 w-24 rounded-full object-cover">
				<div>
					<div class="flex space-x-1 items-center">
						@include('partials.permission-tag', $user)
						<h2 class="text-3xl font-bold">
							{{ $user->name }}
							<span class="text-gray-500 text-lg font-normal">{{ $user->username }}</span>
						</h2>
					</div>
					@if(Auth::check() && Auth::user()->id == $user->id)
					<span class="text-gray-500 text-lg font-normal">{{ $user->email }}</span>
					@endif
					@if($user->bio)
					<p class="text-gray-700 text-lg mt-2">{{ $user->bio }}</p>
					@endif
				</div>
			</div>
			<div class="flex flex-col space-y-2 items-end">
				@if (Auth::check() && Auth::user()->id == $user->id)
				<a href="{{ route('profile.edit') }}" class="mt-5 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
					Edit Profile
				</a>
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					<fieldset>
						<legend class="sr-only">Log Out</legend>
						<button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
							Log Out
						</button>
					</fieldset>
				</form>
				@elseif (Auth::check() && Auth::user()->isAdmin())
				<a href="{{ url('/users/' . $user->id . '/edit') }}" class="mt-5 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
					Edit Profile
				</a>
				@endif
			</div>
		</div>

		<!-- Toggle Tabs -->
		<div class="mb-6 border-b border-gray-200">
			<ul class="flex -mb-px text-lg font-medium">
				<li class="mr-4">
					<button
						id="questions-tab"
						class="tab-btn bg-blue-100 text-blue-600 border-b-2 border-blue-600 px-4 py-2 rounded-t-lg hover:bg-blue-200"
						onclick="showProfileTab('questions')">
						Posted Questions
					</button>
				</li>
				<li class="mr-4">
					<button
						id="answers-tab"
						class="tab-btn bg-gray-100 text-gray-600 border-b-2 border-transparent px-4 py-2 rounded-t-lg hover:bg-gray-200"
						onclick="showProfileTab('answers')">
						Posted Answers
					</button>
				</li>
				<li>
					<button
						id="medals-tab"
						class="tab-btn bg-gray-100 text-gray-600 border-b-2 border-transparent px-4 py-2 rounded-t-lg hover:bg-gray-200"
						onclick="showProfileTab('medals')">
						Medals
					</button>
				</li>
			</ul>
		</div>

		<!-- Questions Section -->
		<div id="questions-section" class="tab-content">
			@if($questions->isEmpty())
			<p class="text-gray-700">No questions posted.</p>
			@else
			<div class="space-y-4">
				@foreach($questions as $question)
				<div>
					@include('partials.question-card', ['question' => $question])
				</div>
				@endforeach
			</div>
			@endif
		</div>
		<!-- Answers Section  -->
		<div id="answers-section" class="tab-content hidden">
			@if($answers->isEmpty())
			<p class="text-gray-700">No answers posted.</p>
			@else
			<div class="space-y-4">
				@foreach($answers as $answer)
				<div>
					@include('partials.answer-card', ['answer' => $answer])
				</div>
				@endforeach
			</div>
			@endif
		</div>
		<!-- Medals Section -->
		<div id="medals-section" class="tab-content hidden">
				@include('partials.medals', ['medals' => $medals])
		</div>
	</div>
</section>
@endsection
