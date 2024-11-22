@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
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
					<button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
						Log Out
					</button>
				</form>
				<form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
					@csrf
					@method('DELETE')
					<button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
						Delete Account
					</button>
				</form>
				@endif
			</div>
		</div>

		<!-- Toggle Tabs -->
		<div class="mb-6 border-b border-gray-200">
			<ul class="flex -mb-px text-lg font-medium">
				<li class="mr-4">
					<button
						id="questions-tab"
						class="tab-btn bg-blue-100 text-blue-600 border-b-2 border-blue-600 px-4 py-2 rounded-t-lg"
						onclick="showTab('questions')">
						Posted Questions
					</button>
				</li>
				<li>
					<button
						id="answers-tab"
						class="tab-btn bg-gray-100 text-gray-600 border-b-2 border-transparent px-4 py-2 rounded-t-lg hover:bg-gray-200"
						onclick="showTab('answers')">
						Posted Answers
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
	</div>
</div>

<script>
	function showTab(tab) {

		document.getElementById('questions-section').classList.add('hidden');
		document.getElementById('answers-section').classList.add('hidden');


		document.querySelectorAll('.tab-btn').forEach(btn => {
			btn.classList.remove('bg-blue-100', 'text-blue-600', 'border-blue-600');
			btn.classList.add('bg-gray-100', 'text-gray-600', 'border-transparent');
		});

		document.getElementById(`${tab}-section`).classList.remove('hidden');
		document.getElementById(`${tab}-tab`).classList.add('bg-blue-100', 'text-blue-600', 'border-blue-600');
	}


	document.addEventListener('DOMContentLoaded', () => showTab('questions'));
</script>


@endsection