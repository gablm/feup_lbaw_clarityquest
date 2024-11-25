@php
$post = $answer->post;
$user = $post->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$q_owner = $answer->question->post->user && Auth::check() && $answer->question->post->user->id == Auth::user()->id;
$owner = $user && Auth::check() && $user->id == Auth::user()->id;
$elevated = Auth::check() && Auth::user()->isElevated();


$edited_at = $answer->post->isEdited();
$is_edited = $edited_at ? " [edited at $edited_at]" : "";

@endphp

<article id="answer" class="mt-2" data-id="{{ $post->id }}">
	<div class="flex flex-row items-center space-x-6 text-gray-500 text-sm mb-2">
		<a class="tool-link" href="{{ $user ? url('/users/' . $user->id) : '/' }}">
			<div class="flex flex-row items-center">
				<img
					src="{{ $profile_pic }}"
					alt="Profile Picture"
					class="w-6 h-6 rounded-full object-cover">
				<span class="ml-2">{{ $user->name ?? "[REDACTED]" }}</span>
			</div>
		</a>
		<span>{{ $post->creationFTime() }} {{ $is_edited }}</span>
		@if($answer->correct)
		<a class="ml-4 tag-link">Marked as correct</a>
		@endif
	</div>
	<p class="text-gray-700 pb-2 pl-3 break-words">{{ $post->text }}</p>
	<div class="flex before:items-center">
		<div class="space-x-1">
			<button onclick="sendVoteRequest({{ $answer->id }}, true)" class="vote-link fa-solid fa-up-long hover:text-red-600"></button>
			<span id="votes-{{ $answer->id }}" class="vote-count">{{ $answer->post->votes }}</span>
			<button onclick="sendVoteRequest({{ $answer->id }}, false)" class="vote-link fa-solid fa-down-long hover:text-blue-500"></button>
		</div>
		@if (Auth::check())
		<button onclick="showCreateCommentModal({{ $answer->id }})" class="tool-link">
			<i class="fa-solid fa-plus"></i>
			<span class="max-sm:hidden ml-1">Comment</span>
		</button>
		@endif
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
		<button onclick="showEditPostModal('answer', {{ $post->id }}, '{{ $post->text }}')" class="tool-link">
			<i class="fa-solid fa-pencil"></i>
			<span class="max-sm:hidden ml-1">Edit</span>
		</button>
		<button data-id="{{ $answer->post->id }}" onclick="deleteAnswer(this)" class="tool-link text-red-500">
			<i class="fa-solid fa-trash"></i>
			<span class="max-md:hidden ml-1">Delete</span>
		</button>
		@endif
	</div>
	<div id="comment-list-{{ $answer->id }}" class="pt-2 pl-4">
		@foreach ($answer->comments as $comment)
		@include('partials.comment', $comment)
		@endforeach
	</div>
</article>