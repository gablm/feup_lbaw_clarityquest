@php
$profile_pic = Auth::user()->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$post = $comment->post;
$owner = $post->user && Auth::check() && $post->user->id == Auth::user()->id;
@endphp

<article data-id="{{ $post->id }}">
	<p class="text-gray-700 my-3">{{ $post->text }}</p>
	<div class="flex space-x-2">
		<div class="space-x-1">
			<a href=# class="vote-link fa-solid fa-up-long"></a>
			<span>{{ $comment->post->votes }}</span>
			<a href=# class="vote-link fa-solid fa-down-long"></a>
		</div>
		@if ($owner)
		<a href=# class="tool-link">Edit</a>
		<a href=# class="tool-link text-red-500">Delete</a>
		@endif
	</div>
	<div class="flex flex-row items-center text-gray-500 text-sm">
		<span class="mr-2">By</span>
		<img
			src={{ $profile_pic }}
			alt="Profile Picture"
			class="w-4 h-4 rounded-full object-cover">
		<span class="ml-1">{{ $post->user->name ?? "[REDACTED]" }}</span>
	</div>
</article>
@endif