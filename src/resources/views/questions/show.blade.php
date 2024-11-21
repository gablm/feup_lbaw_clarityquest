@extends('layouts.app')

@section('title', $question->title)

@section('content')

@php
$answers_count = $question->answers->count();
$comment_count = $question->comments->count();
@endphp

<div class="flex flex-row flex-grow">
	@include('partials.sidebar')
	<div data-id="{{ $question->id }}" class="flex flex-col container mx-auto p-8">
		@include('partials.question', $question)

		@if ($comment_count)
		<div class="py-4 pl-4">
		@foreach ($question->comments as $comment)
		@include('partials.comment', $comment)
		@endforeach
	</div>
		@endif
		<h2 id="question-answer-count" class="text-1xl mt-4">{{ $answers_count }} answers</h2>
		<div class="flex flex-col space-y-2">
			@foreach ($question->answers as $answer)
			@include('partials.answer', $answer)
			@endforeach
		</div>
	</div>
</div>

@endsection