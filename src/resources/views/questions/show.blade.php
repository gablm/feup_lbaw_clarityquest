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

        <div id="comment-list-{{ $question->id }}" class="pt-2 pl-4">
            @foreach ($question->comments as $comment)
            @include('partials.comment', $comment)
            @endforeach
        </div>
        <h2 class="text-1xl mt-2 mb-2"><span id="question-answer-count">{{ $answers_count }}</span> answer(s)</h2>
        @if (Auth::check())
        <div class="flex flex-row space-x-2 mt-2">
            <textarea class="auth focus:outline-none focus:shadow-outline resize-none" id="answer-text" type="textarea" name="text" required placeholder="Enter your answer here..."></textarea>
            <button type="submit" class="nav-main text-blue-700" onclick="sendCreateAnswerRequest()">
                <i class="fa-solid fa-plus"></i>
                <span class="max-sm:hidden ml-1">Add Answer</span>
            </button>
        </div>
        <p id="answer-create-err" class="hidden text-sm text-red-500 mt-2">Content can't be empty!</p>
        <div class="mb-4"></div>
        @endif
        @include('partials.answer-list', ['answerList' => $question->answers])
        <div id="add-comment" class="hidden modal modal-style">
            <div class="modal-overlay modal-bg"></div>
            <div class="modal-container modal-cont">
                <div class="modal-content py-4 text-left px-6">
                    <p class="text-2xl font-bold mb-4">Add Comment</p>
                    <textarea class="auth focus:outline-none focus:shadow-outline resize-none" rows="3" id="text" type="textarea" name="text" required placeholder="Enter your comment here..."></textarea>
                    <div class="mt-4 flex space-x-2 justify-end">
                        <button class="modal-close tool-link" onclick="closeCreateCommentModal()">Cancel</button>
                        <button class="nav-main" onclick="sendCreateCommentRequest()">Comment</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="edit-post" class="hidden modal modal-style">
            <div class="modal-overlay modal-bg"></div>
            <div class="modal-container modal-cont">
                <div class="modal-content py-4 text-left px-6">
                    <p id="edit-title" class="text-2xl font-bold mb-4">Edit ??</p>
                    <div class="mb-4">
                        <textarea class="auth focus:outline-none focus:shadow-outline resize-none" rows="3" id="text" type="textarea" name="text" required placeholder="Edit your content here..."></textarea>
                    </div>
                    <div class="mt-4 flex space-x-2 justify-end">
                        <button class="modal-close tool-link" onclick="closeEditPostModal()">Cancel</button>
                        <button class="nav-main" onclick="sendEditPostRequest()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection