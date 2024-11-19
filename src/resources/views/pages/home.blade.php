@extends('layouts.app')

@section('content')
<div class="flex flex-row">
	<section class="bg-gray-100 px-4">
		@include('partials.sidebar')
	</section>
	<div class="container mx-auto col-span-9 p-4">
		<div class="mb-4">
			<h3 class="text-xl font-semibold mb-2">Top</h3>
			<div class="bg-white shadow-md rounded-lg mb-3">
				<div class="p-4">
					<h5 class="text-lg font-semibold">Example Question Title</h5>
					<p class="text-gray-700">This is a sample description for the top question.</p>
					<small class="text-gray-500">By John Doe | 0 Answers | 0 Comments</small>
				</div>
			</div>
		</div>

		<div class="mb-4">
            <h3 class="text-xl font-semibold mb-2">Top Questions</h3>
            @if($topQuestions->isEmpty())
                <p class="text-gray-700">No top questions available.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($topQuestions as $question)
                        <div class="bg-white shadow-md rounded-lg p-4">
                            <h4 class="text-lg font-semibold mb-2">{{ $question->title }}</h4>
                            <p class="text-gray-700 mb-2">{{ Str::limit($question->post->text, 100) }}</p>
                            <div class="text-gray-500 text-sm">
                                <span>Asked by {{ $question->post->user->name ?? "[REDACTED]" }}</span> |
                                <span>{{ $question->answers->count() }} Answers</span> |
                                <span>{{ $question->post->comments->count() }} Comments</span>
                            </div>
                            <div class="mt-2">
                                @foreach($question->tags as $tag)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

		<div class="mb-4">
            <h3 class="text-xl font-semibold mb-2">Latest Questions</h3>
            @if($latestQuestions->isEmpty())
                <p class="text-gray-700">No latest questions available.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($latestQuestions as $question)
                        <div class="bg-white shadow-md rounded-lg p-4">
                            <h4 class="text-lg font-semibold mb-2">{{ $question->title }}</h4>
                            <p class="text-gray-700 mb-2">{{ Str::limit($question->post->text, 100) }}</p>
                            <div class="text-gray-500 text-sm">
                                <span>Asked by {{  $question->post->user->name ?? "[REDACTED]" }}</span> |
                                <span>{{ $question->answers->count() }} Answers</span> |
                                <span>{{ $question->post->comments->count() }} Comments</span>
                            </div>
                            <div class="mt-2">
                                @foreach($question->tags as $tag)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
	</div>
</div>
@endsection