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
		<h2 id="question-answer-count" class="text-1xl mb-2">{{ $answers_count }} answers</h2>
		@if (Auth::check())
		<div class="flex flex-row space-x-2 mt-2">
			<textarea class="auth focus:outline-none focus:shadow-outline" id="answer-text" type="textarea" name="text" required></textarea>
			<button type="submit" class="nav-main text-blue-700" onclick="sendCreateAnswerRequest()">
				<i class="fa-solid fa-plus"></i>
				<span class="max-sm:hidden ml-1">Add Answer</span>
			</button>
		</div>
		<p id="answer-create-err" class="hidden text-sm text-red-500 mt-2">Content can't be empty!</p>
		<div class="mb-4"></div>
		@endif
		<div id="answer-list" class="flex flex-col space-y-8">
			@foreach ($question->answers as $answer)
			@include('partials.answer', $answer)
			@endforeach
		</div>
	</div>
</div>

@endsection