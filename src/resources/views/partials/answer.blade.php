@php
$post = $answer->post;
$user = $post->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$q_owner = $answer->question->post->user && Auth::check() && $answer->question->post->user->id == Auth::user()->id;
$owner = $user && Auth::check() && $user->id == Auth::user()->id;
$elevated = Auth::check() && Auth::user()->isElevated();
@endphp

<article id="answer" class="mt-2" data-id="{{ $post->id }}">
	<div class="flex flex-row items-center space-x-6 text-gray-500 text-sm mb-2">
		<a class="tool-link" href="{{ $user ? url('/user/' . $user->id) : '/' }}">
			<div class="flex flex-row items-center">
				<img
					src="{{ $profile_pic }}"
					alt="Profile Picture"
					class="w-6 h-6 rounded-full object-cover">
				<span class="ml-2">{{ $user->name ?? "[REDACTED]" }}</span>
			</div>
		</a>
		<span>{{ $post->creationFTime() }}</span>
		@if($answer->correct)
		<a class="ml-4 tag-link">Marked as correct</a>
		@endif
	</div>
	<p class="text-gray-700 my-2 ml-3">{{ $post->text }}</p>
	<div class="flex before:items-center">
		<div class="space-x-1">
			<a href=# class="vote-link fa-solid fa-up-long hover:text-red-600"></a>
			<span>{{ $post->votes }}</span>
			<a href=# class="vote-link fa-solid fa-down-long hover:text-blue-500"></a>
		</div>
		@if (Auth::check())
		<a href=# class="tool-link">
			<i class="fa-solid fa-plus"></i>
			<span class="max-sm:hidden ml-1">Comment</span>
		</a>
		@if ($owner == false && $post->user)
		<a href=# class="tool-link">
			<i class="fa-solid fa-flag"></i>
			<span class="max-sm:hidden ml-1">Report</span>
		</a>
		@endif
		@if ($q_owner && $answer->correct == false)
		<a href=# class="tool-link text-blue-700">
			<i class="fa-solid fa-check"></i>
			<span class="max-md:hidden ml-1">Mark as Correct</span>
		</a>
		@endif
		@if ($owner || $elevated)
		<button onclick="showEditAnswerModal({{ $post->id }})" class="tool-link">
			<i class="fa-solid fa-pencil"></i>
			<span class="max-sm:hidden ml-1">Edit</span>
		</button>
		<button data-id="{{ $answer->post->id }}" onclick="deleteAnswer(this)" class="tool-link text-red-500">
			<i class="fa-solid fa-trash"></i>
			<span class="max-md:hidden ml-1">Delete</span>
		</button>
		@endif
		<div id="answer-edit" class="hidden modal fixed w-full h-full top-0 left-0 flex items-center justify-center">
		<div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
		<div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
			<div class="modal-content py-4 text-left px-6">
				<p class="text-2xl font-bold mb-4">Edit Answer</p>
				<div class="mb-4">
					<textarea class="auth focus:outline-none focus:shadow-outline" id="text" type="textarea" name="text" required>{{ $answer->post->text }}</textarea>
				</div>
				<div class="mt-4 flex space-x-2 justify-end">
					<button class="modal-close tool-link" onclick="closeEditAnswerModal()">Cancel</button>
					<button class="nav-main" onclick="sendEditAnswerRequest({{ $post->id }})">Save</button>
				</div>
			</div>
		</div>
		@endif
	</div>
	@if ($answer->comments->count())
	<div class="py-4 pl-4">
		@foreach ($answer->comments as $comment)
		@include('partials.comment', $comment)
		@endforeach
	</div>
	@endif
</article>