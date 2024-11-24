@php
$post = $question->post;
$user = $post->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$edited_at = $question->post->isEdited();
$is_edited = $edited_at ? " [edited at $edited_at]" : "";

$owner = $post->user && Auth::check() && $post->user->id == Auth::user()->id;
$elevated = Auth::check() && Auth::user()->isElevated();
@endphp

<article id="question" data-id="{{ $question->id }}">
	<div class="flex flex-row items-center space-x-6 text-gray-500 text-md mb-2">
		<a class="tool-link" href="{{ $user ? url('/user/' . $user->id) : '/' }}">
			<div class="flex flex-row items-center">
				<img
					src="{{ $profile_pic }}"
					alt="Profile Picture"
					class="w-6 h-6 rounded-full object-cover">
				<span class="ml-2">{{ $user->name ?? "[REDACTED]" }}</span>
			</div>
		</a>
		<span>{{ $post->creationFTime() }} {{ $is_edited }}</span>
	</div>
	<h2 class="text-4xl font-semibold pl-3 break-words">{{ $question->title }}</h2>
	<p class="text-gray-700 py-3 pl-3 break-words">{{ $question->post->text }}</p>
	<div class="flex items-center">
		<div class="space-x-1">
			<a href=# class="vote-link fa-solid fa-up-long hover:text-red-600"></a>
			<span>{{ $post->votes }}</span>
			<a href=# class="vote-link fa-solid fa-down-long hover:text-blue-500"></a>
		</div>
		@if (Auth::check())
		<button onclick="showCreateCommentModal({{ $question->id }})" class="tool-link">
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
		@endif
	</div>
	@if ($question->tags->count())
	<div class="mt-2">
		@foreach($question->tags as $tag)
		@include('partials.tag', $tag)
		@endforeach
	</div>
	@endif
</article>