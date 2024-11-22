@php
$post = $comment->post;
$user = $post->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$owner = $user && Auth::check() && $user->id == Auth::user()->id;
$elevated = Auth::check() && Auth::user()->isElevated();

@endphp

<article id="comment" class="mt-2" data-id="{{ $post->id }}">
	<div class="flex flex-row items-center space-x-6 text-gray-500 text-sm">
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
	</div>
	<p class="text-gray-700 my-2 ml-3">{{ $post->text }}</p>
	<div class="flex items-center">
		<div class="space-x-1">
			<a href=# class="vote-link fa-solid fa-up-long hover:text-red-600"></a>
			<span>{{ $post->votes }}</span>
			<a href=# class="vote-link fa-solid fa-down-long hover:text-blue-500"></a>
		</div>
		@if ($owner == false && $post->user)
		<a href=# class="tool-link">
			<i class="fa-solid fa-flag"></i>
			<span class="ml-1">Report</span>
		</a>
		@endif
		@if ($owner || $elevated)
		<a href=# class="tool-link">
			<i class="fa-solid fa-pencil"></i>
			<span class="max-sm:hidden ml-1">Edit</span>
		</a>
		<button data-id="{{ $comment->post->id }}" onclick="deleteComment(this)" class="tool-link text-red-500">
			<i class="fa-solid fa-trash"></i>
			<span class="max-md:hidden ml-1">Delete</span>
		</button>
		@endif
	</div>
</article>