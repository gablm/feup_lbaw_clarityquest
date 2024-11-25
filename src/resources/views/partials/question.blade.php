@php
$post = $question->post;
$user = $post->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$edited_at = $question->post->isEdited();
$is_edited = $edited_at ? " [edited at $edited_at]" : "";

$owner = $post->user && Auth::check() && $post->user->id == Auth::user()->id;
$elevated = Auth::check() && Auth::user()->isElevated();
@endphp

<article id="question" data-id="{{ $question->id }}">
	<div class="flex flex-row items-center space-x-6 text-gray-500 text-md mb-2">
		<div class="flex flex-row items-center">
			<a class="tool-link" href="{{ $user ? url('/users/' . $user->id) : '/' }}">
				<div class="flex flex-row items-center">
					<img
						src="{{ $profile_pic }}"
						alt="Profile Picture"
						class="w-6 h-6 rounded-full object-cover">
					<span class="ml-2">{{ $user->name ?? "[REDACTED]" }}</span>
				</div>
			</a>
		</div>
		<span>{{ $post->creationFTime() }} {{ $is_edited }}</span>
		<div>
			@if ($question->tags->count())
			@foreach($question->tags as $tag)
			@include('partials.tag', $tag)
			@endforeach
			@endif
		</div>
	</div>
	<h2 class="text-4xl font-semibold pl-3 break-words">{{ $question->title }}</h2>
	<p class="text-gray-700 py-3 pl-3 break-words">{{ $question->post->text }}</p>
	<div class="flex items-center">
		<div class="space-x-1">
			<button onclick="sendVoteRequest({{ $question->id }}, true)" class="vote-link fa-solid fa-up-long hover:text-red-600"></button>
			<span id="votes-{{ $question->id }}" class="vote-count">{{ $question->post->votes }}</span>
			<button onclick="sendVoteRequest({{ $question->id }}, false)" class="vote-link fa-solid fa-down-long hover:text-blue-500"></button>
		</div>
		@if (Auth::check())
		<button onclick="showCreateCommentModal({{ $question->id }})" class="tool-link">
			<i class="fa-solid fa-plus"></i>
			<span class="max-sm:hidden ml-1">Comment</span>
		</button>
		@include('partials.follow-btn', $question)
		@if ($owner == false && $post->user && Auth::user()->isElevated() == false)
		<a href=# class="tool-link">
			<i class="fa-solid fa-flag"></i>
			<span class="max-md:hidden ml-1">Report</span>
		</a>
		@endif
		@if ($owner || $elevated)
		<button class="tool-link" onclick="showTagModal()">
			<i class="fa-solid fa-tags"></i>
			<span class="max-lg:hidden ml-1">Manage Tags</span>
		</button>
		@endif
		<div id="tag-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
			<div class="bg-white rounded-lg p-6 w-1/3">
				<h2 class="text-xl font-semibold mb-4">Tags</h2>
				<form method="POST" action="{{ url('/questions/' . $question->id . '/tags') }}">
					@csrf
					<div class="mb-4">
						<label for="tag" class="block text-gray-700">Add a Tag from the Tag List</label>
						<select name="tag" id="tag" class="w-full px-3 py-2 border rounded-md">
							@foreach($tags as $tag)
							<option value="{{ $tag->name }}">{{ $tag->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="flex justify-end">
						<button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 mr-2" onclick="closeTagModal()">Cancel</button>
						<button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Add</button>
					</div>
				</form>
				<h3 class="text-lg font-semibold mt-4">Current Tags</h3>
				<ul class="list-disc list-inside">
					@foreach($question->tags as $tag)
					<li class="flex justify-between items-center">
						<span>{{ $tag->name }}</span>
						<form method="POST" action="{{ url('/questions/' . $question->id . '/tags/remove') }}">
							@csrf
							<input type="hidden" name="tag" value="{{ $tag->name }}">
							<button type="submit" class="text-red-500 hover:text-red-700">Remove</button>
						</form>
					</li>
					@endforeach
				</ul>
			</div>
		</div>
		@if ($owner || (Auth::check() && Auth::user()->isAdmin()))
		<button class="tool-link" onclick="showEditQuestionModal()">
			<i class="fa-solid fa-pencil"></i>
			<span class="max-md:hidden ml-1">Edit</span>
		</button>
		@endif
		@if ($owner || $elevated)
		<form method="POST" action="{{ url('/questions/'. $question->id)}}" onsubmit="return confirm('Are you sure you want to delete this question? This action cannot be undone.');">
			@csrf
			@method('DELETE')
			<button type="submit" class="tool-link text-red-500">
				<i class="fa-solid fa-trash"></i>
				<span class="max-md:hidden ml-1">Delete</span>
			</button>
		</form>
		@endif
		@endif
	</div>
	<div id="edit" class="hidden modal fixed w-full h-full top-0 left-0 flex items-center justify-center">
		<div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
		<div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
			<div class="modal-content py-4 text-left px-6">
				<p class="text-2xl font-bold mb-4">Edit</p>
				<div class="mb-4">
					<label class="auth" for="title">Title</label>
					<input class="auth focus:outline-none focus:shadow-outline" id="title" type="text" name="title" value="{{ $question->title }}" required>
				</div>
				<div class="mb-4">
					<label class="auth" for="description">Description</label>
					<textarea class="auth focus:outline-none focus:shadow-outline resize-none" rows="10" id="description" type="textarea" name="description" required>{{ $question->post->text }}</textarea>
				</div>
				<div class="mt-4 flex space-x-2 justify-end">
					<button class="modal-close tool-link" onclick="closeEditQuestionModal()">Cancel</button>
					<button class="nav-main" onclick="sendEditQuestionRequest()">Save</button>
				</div>
			</div>
		</div>
	</div>
</article>