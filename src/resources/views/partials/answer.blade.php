@php
$profile_pic = Auth::user()->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$post = $answer->post;
$owner = $post->user && Auth::check() && $post->user->id == Auth::user()->id;

$comment_count = $answer->comments->count();
@endphp

<article data-id="{{ $post->id }}">
	<p class="text-gray-700 my-3">{{ $post->text }}</p>
	<div class="flex space-x-2">
		<div class="space-x-1">
			<a href=# class="vote-link fa-solid fa-up-long"></a>
			<span>{{ $post->votes }}</span>
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
			src="{{ $profile_pic }}"
			alt="Profile Picture"
			class="w-4 h-4 rounded-full object-cover">
		<span class="ml-1">{{ $post->user->name ?? "[REDACTED]" }}</span>
	</div>
	@if ($comment_count)
	<h2 id="question-comment-count" class="p4 text-1xl mt-4">{{ $answers_count }} comments</h2>
	<div class="p-4">
		@foreach ($question->comments as $comment)
		@include('partials.comment', $comment)
		@endforeach
	</div>
	@endif
</article>
@endif