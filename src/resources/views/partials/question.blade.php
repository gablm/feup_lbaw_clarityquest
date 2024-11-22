@php
$post = $question->post;
$user = $post->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$owner = $post->user && Auth::check() && $post->user->id == Auth::user()->id;
$elevated = Auth::check() && Auth::user()->isElevated();
@endphp

<article id="question" data-id="{{ $question->id }}">
	<div class="flex flex-row items-center space-x-6 text-gray-500 text-md mb-2">
		<div class="flex flex-row items-center">
			<img
				src="{{ $profile_pic }}"
				alt="Profile Picture"
				class="w-6 h-6 rounded-full object-cover">
			<span class="ml-2">{{ $question->post->user->name ?? "[REDACTED]" }}</span>
		</div>
		<span>{{ $post->creationFTime() }}</span>
	</div>
	<h2 class="text-4xl font-semibold ml-3">{{ $question->title }}</h2>
	<p class="text-gray-700 my-3 ml-3">{{ $question->post->text }}</p>
	<div class="flex items-center">
		<div class="space-x-1">
			<a href=# class="vote-link fa-solid fa-up-long hover:text-red-600"></a>
			<span>{{ $post->votes }}</span>
			<a href=# class="vote-link fa-solid fa-down-long hover:text-blue-500"></a>
		</div>
		<button class="tool-link">
			<i class="fa-solid fa-plus"></i>
			<span class="max-sm:hidden ml-1">Comment</span>
		</button>
		<a href=# class="tool-link">
			<i class="fa-solid fa-bell"></i>
			<span class="max-sm:hidden ml-1">Follow</span>
		</a>
		@if ($owner == false && $post->user)
		<a href=# class="tool-link">
			<i class="fa-solid fa-flag"></i>
			<span class="max-sm:hidden ml-1">Report</span>
		</a>
		@endif
		@if ($owner || $elevated)
		<button class="tool-link" onclick="showEditQuestionModal()">
			<i class="fa-solid fa-pencil"></i>
			<span class="max-md:hidden ml-1">Edit</span>
		</button>
		<form method="POST" action="{{ url('/questions/'. $question->id)}}" onsubmit="return confirm('Are you sure you want to delete this question? This action cannot be undone.');">
			@csrf
			@method('DELETE')
			<button type="submit" class="tool-link text-red-500">
				<i class="fa-solid fa-trash"></i>
				<span class="max-md:hidden ml-1">Delete</span>
			</button>
		</form>
		@endif
	</div>
	<div id="edit" class="hidden modal fixed w-full h-full top-0 left-0 flex items-center justify-center">
		<div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
		<div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
			<div class="modal-content py-4 text-left px-6">
				<p class="text-2xl font-bold mb-4">Edit</p>
				<div class="mb-4">
					<label class="auth" for="title">Title</label>
					<input class="auth focus:outline-none focus:shadow-outline" id="title" type="text" name="title" value="{{ $question->title }}" required>
				</div>
				<div class="mb-4">
					<label class="auth" for="description">Description</label>
					<textarea class="auth focus:outline-none focus:shadow-outline" id="description" type="textarea" name="description" value="{{ $question->post->text }}" required></textarea>
				</div>
				<div class="mt-4 flex space-x-2 justify-end">
					<button class="modal-close tool-link" onclick="closeEditQuestionModal()">Cancel</button>
					<button class="nav-main" onclick="sendEditQuestionRequest()">Save</button>
				</div>
			</div>
		</div>
	</div>
	<!--<div id="add" class="hidden modal fixed w-full h-full top-0 left-0 flex items-center justify-center">
		<div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
		<div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
			<div class="modal-content py-4 text-left px-6">
				<p class="text-2xl font-bold mb-4">Add Comment</p>
				<textarea class="auth focus:outline-none focus:shadow-outline" id="description" type="textarea" name="description" value="{{ $question->post->text }}" required></textarea>
			</div>
			<div class="mt-4 flex space-x-2 justify-end">
				<button class="modal-close tool-link" onclick="closeCreateCommentModal()">Cancel</button>
				<button class="nav-main" onclick="sendCreateCommentRequest()">Save</button>
			</div>
		</div>
	</div>-->

	@if ($question->tags->count())
	<div class="mt-2">
		@foreach($question->tags as $tag)
		@include('partials.tag', $tag)
		@endforeach
	</div>
	@endif
</article>