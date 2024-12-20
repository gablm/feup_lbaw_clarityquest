@extends('layouts.app')

@section('title', "Question - {$question->title}")

@php
	$answers_count = $question->answers->count();
	$comment_count = $question->comments->count();

	$crumbs = [
		['name' => 'Home', 'url' => route('home')],
		['name' => "Question #$question->id", 'url' => route('profile')]
	];
@endphp

@section('content')
<div class="flex flex-row flex-grow">
	@include('partials.sidebar')
	<div data-id="{{ $question->id }}" class="flex flex-col container mx-auto p-8">
		{!! breadcrumbs($crumbs) !!}
		@include('partials.question', $question)

		<div id="comment-list-{{ $question->id }}" class="pt-2 pl-4">
			@foreach ($question->comments as $comment)
				@include('partials.comment', $comment)
			@endforeach
		</div>
		<h2 class="text-1xl mt-2 mb-2"><span id="question-answer-count">{{ $answers_count }}</span> answer(s)</h2>
		@if (Auth::check())
			<form class="flex flex-row space-x-2 mt-2" onsubmit="sendCreateAnswerRequest({{ $question->id }}); return false;">
				<textarea onkeyup="charCounter(this.parentElement, this, 500)"
					onkeydown="charCounter(this.parentElement, this, 500)"
					class="auth focus:outline-none focus:shadow-outline resize-y" id="answer-text" type="textarea"
					name="text" maxlength="500" required placeholder="Enter your answer here..."></textarea>
				<button type="submit" class="nav-main text-blue-700">
					<i class="fa-solid fa-plus"></i>
					<span class="max-sm:hidden ml-1">Add Answer</span>
				</button>
			</form>
			<span class="counter my-2">0/500 characters</span>
			<div class="flex flex-col mb-4">
				<span class="ml-2 mb-4 add-err hidden text-sm text-red-500"></span>
			</div>
		@endif
		@include('partials.answer-list', ['answerList' => $question->answers])
		<div id="add-comment" class="hidden modal modal-style">
			<div class="modal-overlay modal-bg"></div>
			<div class="modal-container modal-cont">
				<form class="modal-content py-4 text-left px-6" onsubmit="sendCreateCommentRequest(); return false;">
					<p class="text-2xl font-bold mb-4">Add Comment</p>
					<div class="flex flex-col mb-4">
						<textarea onkeyup="charCounter(this, this, 500)" onkeydown="charCounter(this, this, 500)"
							class="auth focus:outline-none focus:shadow-outline resize-none" rows="3" id="text"
							maxlength="500" type="textarea" name="text" required
							placeholder="Enter your comment here..."></textarea>
						<span class="counter my-1">0/500 characters</span>
						<span class="err hidden auth-error bold mt-1"></span>
					</div>
					<div class="mt-4 flex space-x-2 justify-end">
						<button class="modal-close tool-link" onclick="closeCreateCommentModal()">Cancel</button>
						<button class="nav-main" type="submit">Comment</button>
					</div>
				</form>
			</div>
		</div>
		<div id="edit-post" class="hidden modal modal-style">
			<div class="modal-overlay modal-bg"></div>
			<div class="modal-container modal-cont">
				<form class="modal-content py-4 text-left px-6" onsubmit="sendEditPostRequest(); return false;">
					<p id="edit-title" class="text-2xl font-bold mb-4">Edit ??</p>
					<div class="flex flex-col mb-4">
						<textarea onkeyup="charCounter(this, this, 500)" onkeydown="charCounter(this, this, 500)"
							class="auth focus:outline-none focus:shadow-outline resize-none" rows="3" id="edit-text"
							type="textarea" name="text" maxlength="500" required
							placeholder="Edit your content here..."></textarea>
						<span class="counter my-1">0/500 characters</span>
						<span class="err hidden auth-error bold mt-1"></span>
					</div>
					<div class="mt-4 flex space-x-2 justify-end">
						<button class="modal-close tool-link" onclick="closeEditPostModal()">Cancel</button>
						<button class="nav-main" type="submit">Save</button>
					</div>
				</form>
			</div>
		</div>
		<div id="report-post" class="hidden modal modal-style">
			<div class="modal-overlay modal-bg"></div>
			<div class="modal-container modal-cont">
				<form class="modal-content py-4 text-left px-6" onsubmit="sendReportPostRequest(); return false;">
					<p id="report-edit-title" class="text-2xl font-bold mb-4">Report ??</p>
					<div class="mb-4">
						<p id="report-text">????</p>
					</div>
					<div class="flex flex-col mb-4">
						<label class="auth" for="report-reason">Reason<span class="font-bold text-red-600">*</span></label>
						<textarea onkeyup="charCounter(this, this, 100)" onkeydown="charCounter(this, this, 100)"
							class="auth focus:outline-none focus:shadow-outline resize-none" rows="3" id="report-reason"
							maxlength="100" type="textarea" name="report-reason" required
							placeholder="Enter the reason here..."></textarea>
						<span class="counter my-1">0/100 characters</span>
						<span id="report-error" class="hidden auth-error bold mt-1"></span>
					</div>
					<div class="mt-4 flex space-x-2 justify-end">
						<button class="modal-close tool-link" onclick="closeReportPostModal()">Cancel</button>
						<button class="nav-main" type="submit">Report</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@endsection