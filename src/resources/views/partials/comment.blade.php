@php
	$post = $comment->post;
	$user = $post->user;

	$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

	$owner = $user && Auth::check() && $user->id == Auth::user()->id;
	$elevated = Auth::check() && Auth::user()->isElevated();

	$edited_at = $comment->post->isEdited();
	$is_edited = $edited_at ? " [edited at $edited_at]" : "";
@endphp

<article class="comment mt-2" data-id="{{ $post->id }}">
	<a id="{{ $post->id }}"></a>
	<div class="flex flex-row items-center space-x-6 text-gray-500 text-sm">
		<a class="tool-link" href="{{ $user ? url('/users/' . $user->id) : '/' }}">
			<div class="flex flex-row items-center">
				<img src="{{ $profile_pic }}" alt="Profile Picture" class="w-6 h-6 rounded-full object-cover">
				<span class="ml-2">{{ $user->name ?? "[REDACTED]" }}</span>
			</div>
		</a>
		<span>{{ $post->creationFTime() }}{{$is_edited}}</span>
	</div>
	<p class="text-gray-700 py-2 pl-3 break-words">{{ $post->text }}</p>
	<div class="flex items-center">
		@include('partials.vote', ['id' => $comment->id, 'votes' => $comment->post->votes])
		@if ($owner == false && $post->user && Auth::check() && Auth::user()->isElevated() == false)
			<a href=# class="tool-link">
				<i class="fa-solid fa-flag"></i>
				<span class="ml-1">Report</span>
			</a>
		@endif
		@if ($owner || (Auth::check() && Auth::user()->isAdmin()))
			<button onclick="showEditPostModal('comment', {{ $post->id }}, '{{ $post->text }}')" class="tool-link">
				<i class="fa-solid fa-pencil"></i>
				<span class="max-sm:hidden ml-1">Edit</span>
			</button>
		@endif
		@if ($owner || $elevated)
			<button data-id="{{ $comment->post->id }}" onclick="deleteComment(this)" class="tool-link text-red-500">
				<i class="fa-solid fa-trash"></i>
				<span class="max-md:hidden ml-1">Delete</span>
			</button>
		@endif
	</div>
</article>