@php
$profile_pic = Auth::user()->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');
$post = $question->post;
$owner = $post->user && Auth::check() && $post->user->id == Auth::user()->id;
$elevated = Auth::user()->isElevated();
@endphp

<article data-id="{{ $question->id }}">
	<h2 class="text-4xl font-semibold">{{ $question->title }}</h2>
	<p class="text-gray-700 my-3">{{ $post->text }}</p>
	<div class="flex justify-between items-center">
		<div class="flex">
			<div class="space-x-1">
				<a href=# class="vote-link fa-solid fa-up-long hover:text-red-600"></a>
				<span>{{ $post->votes }}</span>
				<a href=# class="vote-link fa-solid fa-down-long hover:text-blue-500"></a>
			</div>
			<a href=# class="tool-link">
				<i class="fa-solid fa-plus"></i>
				<span class="ml-1">Comment</span>
			</a>
			@if ($owner == false && $post->user)
			<a href=# class="tool-link">
				<i class="fa-solid fa-flag"></i>
				<span class="ml-1">Report</span>
			</a>
			@endif
		</div>
		@if ($owner || $elevated)
		<div>
			<a href=# class="tool-link">
				<i class="fa-solid fa-pencil"></i>
				<span class="ml-1">Edit</span>
			</a>
			<a href=# class="tool-link text-red-500">
				<i class="fa-solid fa-trash"></i>
				<span class="ml-1">Delete</span>
			</a>
		</div>
		@else
		<div class="flex flex-row items-center text-gray-500 text-sm mr-3">
			<span class="mr-2">By</span>
			<img
				src="{{ $profile_pic }}"
				alt="Profile Picture"
				class="w-4 h-4 rounded-full object-cover">
			<span class="ml-1">{{ $post->user->name ?? "[REDACTED]" }}</span>
		</div>
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