@php
$profile_pic = Auth::user()->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$post = $answer->post;
$owner = $post->user && Auth::check() && $post->user->id == Auth::user()->id;
$q_owner = $answer->question->post->user && Auth::check() && $answer->question->post->user->id == Auth::user()->id;
$elevated = Auth::user()->isElevated();
@endphp

<article class="mt-2" data-id="{{ $post->id }}">
	<div class="flex flex-row justify-between items-center space-x-6 text-gray-500 text-sm mb-2">
		<div class="flex flex-row items-center">
			<img
				src="{{ $profile_pic }}"
				alt="Profile Picture"
				class="w-4 h-4 rounded-full object-cover">
			<span class="ml-1">{{ $post->user->name ?? "[REDACTED]" }}</span>
			@if($answer->correct)
			<a class="ml-4 tag-link">Marked as correct</a>
			@endif
		</div>
		<span>{{ $post->creationFTime() }}</span>
	</div>
	<p class="text-gray-700 my-2 ml-3">{{ $post->text }}</p>
	<div class="flex before:items-center">
		<div class="space-x-1">
			<a href=# class="vote-link fa-solid fa-up-long hover:text-red-600"></a>
			<span>{{ $post->votes }}</span>
			<a href=# class="vote-link fa-solid fa-down-long hover:text-blue-500"></a>
		</div>
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
		@if ($q_owner)
		<a href=# class="tool-link text-blue-700">
			<i class="fa-solid fa-check"></i>
			<span class="max-md:hidden ml-1">Mark as Correct</span>
		</a>
		@endif
		@if ($owner || $elevated)
		<a href=# class="tool-link">
			<i class="fa-solid fa-pencil"></i>
			<span class="max-md:hidden ml-1">Edit</span>
		</a>
		<a href=# class="tool-link text-red-500">
			<i class="fa-solid fa-trash"></i>
			<span class="max-md:hidden ml-1">Delete</span>
		</a>
		@endif
	</div>
	<div class="py-4 pl-4">
		@foreach ($answer->comments as $comment)
		@include('partials.comment', $comment)
		@endforeach
	</div>
</article>