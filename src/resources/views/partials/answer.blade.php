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

<article class="answer mt-2" data-id="{{ $post->id }}">
	<a id="{{ $post->id }}"></a>
	<div class="flex flex-row items-center space-x-6 text-gray-500 text-sm mb-2">
		<a class="tool-link" href="{{ $user ? url('/users/' . $user->id) : '/' }}">
			<div class="flex flex-row items-center">
				<img src="{{ $profile_pic }}" alt="Profile Picture" class="w-6 h-6 rounded-full object-cover">
				<span class="ml-2">{{ $user->name ?? "[REDACTED]" }}</span>
			</div>
		</a>
		<span>{{ $post->creationFTime() }} {{ $is_edited }}</span>
		@if($answer->correct)
			<a class="ml-4 correct">Marked as correct</a>
		@endif
	</div>
	<p class="text-gray-700 pb-2 pl-3 break-words">{{ $post->text }}</p>
	<div class="flex before:items-center">
		@include('partials.vote', ['id' => $answer->id, 'votes' => $answer->post->votes, 'voteStatus' => Auth::check() ? $answer->post->voteStatus(Auth::id()) : null])
		@if (Auth::check())
			<button onclick="showCreateCommentModal({{ $answer->id }})" class="tool-link">
				<i class="fa-solid fa-plus"></i>
				<span class="max-sm:hidden ml-1">Comment</span>
			</button>
		@endif
		@if ($owner == false && $post->user && Auth::check())
			<button href=# class="tool-link" onclick="showReportPostModal('answer', {{ $answer->id }}, '{{ $post->text }}')">
				<i class="fa-solid fa-flag"></i>
				<span class="max-md:hidden ml-1">Report</span>
			</button>
		@endif
		@if ($q_owner && !$answer->correct)
			<a href="javascript:void(0);" onclick="markAsCorrect({{ $answer->id }})"
				class="tool-link text-blue-700 mark-as-correct-btn">
				<i class="fa-solid fa-check"></i>
				<span class="max-lg:hidden ml-1">Mark as Correct</span>
			</a>
		@endif
		@if ($owner || (Auth::check() && Auth::user()->isAdmin()))
			<button onclick="showEditPostModal('answer', {{ $post->id }}, '{{ $post->text }}')" class="tool-link">
				<i class="fa-solid fa-pencil"></i>
				<span class="max-sm:hidden ml-1">Edit</span>
			</button>
		@endif
		@if ($owner || $elevated)
			<button onclick="showDeleteModal({{ $answer->id }}, deleteAnswer, setupDeleteAnswer)" class="tool-link text-red-500">
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
</a>