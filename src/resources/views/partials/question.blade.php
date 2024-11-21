@php
$profile_pic = Auth::user()->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');
$owner = $question->post->user && Auth::check() && $question->post->user->id == Auth::user()->id;
@endphp

<article data-id="{{ $question->id }}">
	<h2 class="text-4xl font-semibold">{{ $question->title }}</h2>
	<div class="flex flex-row items-center text-gray-500 text-sm">
		<span class="mr-2">By</span>
		<img
			src={{ $profile_pic }}
			alt="Profile Picture"
			class="w-4 h-4 rounded-full object-cover">
		<span class="ml-1">{{ $question->post->user->name ?? "[REDACTED]" }}</span>
	</div>
	<p class="text-gray-700 my-3">{{ $question->post->text }}</p>
	<div class="flex space-x-2">
		<div class="space-x-1">
			<a href=# class="vote-link fa-solid fa-up-long"></a>
			<span>{{ $question->post->votes }}</span>
			<a href=# class="vote-link fa-solid fa-down-long"></a>
		</div>
		@if ($owner)
		<a href=# class="tool-link">Edit</a>
		<a href=# class="tool-link text-red-500">Delete</a>
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