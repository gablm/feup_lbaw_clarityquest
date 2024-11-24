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

<<<<<<< Updated upstream
		@if ($comment_count)
		<div class="py-4 pl-4">
			@foreach ($question->comments as $comment)
			@include('partials.comment', $comment)
			@endforeach
		</div>
		@endif
		<h2 class="text-1xl mt-2 mb-2"><span id="question-answer-count">{{ $answers_count }}</span> answer(s)</h2>
		@if (Auth::check())
		<div class="flex flex-row space-x-2 mt-2">
			<textarea class="auth focus:outline-none focus:shadow-outline resize-none" id="answer-text" type="textarea" name="text" required></textarea>
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
=======
        @if ($comment_count)
        <div class="py-4 pl-4" id="comment-list-{{ $question->id }}">
            @foreach ($question->comments as $comment)
            @include('partials.comment', ['comment' => $comment])
            @endforeach
        </div>
        @endif

        @if (Auth::check())
        <div class="mt-4">
            <form data-post-id="{{ $question->id }}" onsubmit="event.preventDefault(); sendCreateCommentRequest();">
                @csrf
                <div class="mb-4">
                    <label for="comment-text-{{ $question->id }}" class="block text-gray-700">Add a Comment</label>
                    <textarea id="comment-text-{{ $question->id }}" class="comment-text w-full px-3 py-2 border rounded-md" placeholder="Enter your comment"></textarea>
                    <div id="comment-create-err-{{ $question->id }}" class="comment-create-err hidden text-red-500 mt-2">Error creating comment.</div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                </div>
            </form>
        </div>
        @endif

        <h2 class="text-1xl mt-2 mb-2"><span id="question-answer-count">{{ $answers_count }}</span> answer(s)</h2>
        @if (Auth::check())
        <div class="flex flex-row space-x-2 mt-2">
            <textarea class="auth focus:outline-none focus:shadow-outline resize-none" id="answer-text" type="textarea" name="text" required></textarea>
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
>>>>>>> Stashed changes
</div>

@endsection