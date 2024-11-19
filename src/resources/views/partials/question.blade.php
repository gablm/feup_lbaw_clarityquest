<article class="mb-4" data-id="{{ $question->id }}">
	<div class="bg-white shadow-md rounded-lg mb-3">
		<div class="p-4">
			<h5 class="text-lg font-semibold">{{ $question->title }}</h5>
			<p class="text-gray-700">{{ $question->post->text }}</p>
			<small class="text-gray-500">By {{ $question->post->owner->name }}</small>
		</div>
	</div>
</article>