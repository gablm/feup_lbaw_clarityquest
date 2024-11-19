<article class="w-full bg-white shadow-md rounded-lg mb-4" data-id="{{ $question->id }}">
	@if ($details)
	<a href={{ url('/questions/' . $question->id) }}>
	@endif	
	<div class="flex flex-row">
		<div class="p-4">
			<h5 class="text-lg font-semibold mb-2">{{ $question->title }}</h5>
			<p class="text-gray-700">{{ $question->post->text }}</p>
			<div class="text-gray-500 text-sm mt-4">
				<span class="text-gray-500">By {{ $question->post->owner->name ?? "[REDACTED]" }}</span>
				@if ($details)
				<span>| {{ $question->answers->count() }} Answers</span>
				<span>| {{ $question->post->comments->count() }} Comments</span>
				@endif
			</div>
			
		</div>
		<div class="mt-2">
			@foreach($question->tags as $tag)
			<span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $tag->name }}</span>
			@endforeach
		</div>
	</div>
	@if ($details)
	</a>
	@endif
</article>