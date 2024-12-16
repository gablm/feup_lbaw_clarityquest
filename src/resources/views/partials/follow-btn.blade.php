<button onclick="followQuestion(this)" class="tool-link" data-id="{{ $question->id }}">
	@if ($question->isFollowed(Auth::user()))
	<i class="fa-solid fa-check"></i>
	<span class="max-md:hidden ml-1">Followed</span>
	@else
	<i class="fa-solid fa-bell"></i>
	<span class="max-md:hidden ml-1">Follow</span>
	@endif
</button>