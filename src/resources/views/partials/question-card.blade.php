@php
$profile_pic = Auth::user()->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$post = $question->post;
$owner = $post->user && Auth::check() && $post->user->id == Auth::user()->id;
$elevated = Auth::user()->isElevated();
@endphp

<article class="w-full bg-white shadow-md rounded-lg mb-4" data-id="{{ $question->id }}">
	<a href={{ url('/questions/' . $question->id) }}>
		<div class="flex flex-col p-4">
			<h5 class="text-lg font-semibold">{{ $question->title }}</h5>
			<div class="flex flex-row items-center text-gray-500 text-sm">
				<span class="mr-2">By</span>
				<img
					src="{{ $profile_pic }}"
					alt="Profile Picture"
					class="w-4 h-4 rounded-full object-cover">
				<span class="ml-1">{{ $question->post->user->name ?? "[REDACTED]" }}</span>
			</div>
			<p class="text-gray-700 my-3">{{ $question->post->text }}</p>
			<div class="flex flex-row justify-between space-x-6 text-gray-500 text-sm">
				<div class="flex flex-row space-x-6">
					<div>
						<i class="fa-solid fa-up-down"></i>
						<span>{{ $question->post->votes }}</span>
					</div>
					<span> {{ $question->answers->count() }} Answers</span>
					<span>{{ $post->comments->count() }} Comments</span>
				</div>
				<div>
					<span>{{ $post->creationDate() }} at {{ $post->creationTime() }}</span>
				</div>
			</div>
			@if ($question->tags->count())
			<div class="mt-2">
				@foreach($question->tags as $tag)
				@include('partials.tag', $tag)
				@endforeach
			</div>
			@endif
		</div>
	</a>
</article>