<article class="w-full bg-white shadow-md rounded-lg mb-4" data-id="{{ $question->id }}">
	<a href={{ url('/questions/' . $question->id) }}>
		<div class="flex flex-row">
			<div class="p-4">
				<h5 class="text-lg font-semibold">{{ $question->title }}</h5>
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
				<div class="flex flex-row justify-between space-x-6 text-gray-500 text-sm">
					<div>
						<i class="fa-solid fa-up-down"></i>
						<span>{{ $question->post->votes }}</span>
					</div>
					<div>
						<span>{{ $question->answers->count() }} Answers</span>
						<span>| {{ $question->post->comments->count() }} Comments</span>
					</div>
				</div>
			</div>
			<div class="mt-2">
				@foreach($question->tags as $tag)
				<span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $tag->name }}</span>
				@endforeach
			</div>
		</div>
	</a>
</article>