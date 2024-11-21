<div class="flex flex-col p-4">
	<h2 class="text-4xl font-semibold">{{ $question->title }}</h2>
	<div class="flex flex-row items-center text-gray-500 text-sm">
		<span class="mr-2">By</span>
		<img
			@if (Auth::user()->profile_pic)
		src="{{ asset($user->profile_pic) }}"
		@else
		src="{{ url('img/default_pic.png') }}"
		@endif
		alt="Profile Picture"
		class="w-4 h-4 rounded-full object-cover">
		<span class="ml-1">{{ $question->post->user->name ?? "[REDACTED]" }}</span>
	</div>
	<p class="text-gray-700 my-3">{{ $question->post->text }}</p>
	<div class="flex space-x-6 text-gray-500 text-sm">
		<div>
			<i class="fa-solid fa-up-down"></i>
			<span>{{ $question->post->votes }}</span>
		</div>
		@if ($question->post->user && Auth::check() && $question->post->user->id == Auth::user()->id)
		<span>Edit</span>
		<span class="text-red-500">Delete</span>
		@endif
	</div>
	@if ($question->tags->count())
	<div class="mt-2">
		@foreach($question->tags as $tag)
		@include('partials.tag', $tag)
		@endforeach
	</div>
	@endif
	@if ($question->comments->count())
	<div class="p-4">
		@foreach ($question->comments as $comment)
		@include('partials.comment', $comment)
		@endforeach
	</div>
	@endif
	<h2 class="text-1xl mt-4">{{ $question->answers->count() }} answers</h2>
	@foreach ($question->answers as $answer)
	@include('partials.answer', $answer)
	@endforeach
</div>