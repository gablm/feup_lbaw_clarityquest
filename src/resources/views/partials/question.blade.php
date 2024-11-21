<div class="flex flex-row">
	<div class="p-4">
		<h2 class="text-3xl font-semibold">{{ $question->title }}</h2>
		<div class="flex flex-row items-center text-gray-500 text-sm">
			<span class="mr-2">By</span>
			@include('partials.profile-picture', ['user' => $question->user, 'size' => 4])
			<span class="ml-1">{{ $question->post->user->name ?? "[REDACTED]" }}</span>
		</div>
		<p class="text-gray-700 my-3">{{ $question->post->text }}</p>
		<div class="flex flex-row justify-between space-x-6 text-gray-500 text-sm">
			<div>
				<i class="fa-solid fa-up-down"></i>
				<span>{{ $question->post->votes }}</span>
			</div>
			@if ($question->user && Auth::check() && $question->user->id == Auth::user()->id)
			<div>
				<span>Edit</span>
				<span>Delete</span>
			</div>
			@endif
		</div>
	</div>
	<div class="mt-2">
		@foreach($question->tags as $tag)
		<span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $tag->name }}</span>
		@endforeach
	</div>
</div>