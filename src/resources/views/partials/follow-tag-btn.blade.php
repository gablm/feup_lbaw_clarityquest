<button onclick="followTag(this)" class="tool-link" data-id="{{ $tag->id }}">
	@if ($tag->isFollowed(Auth::user()))
	<i class="fa-solid fa-check"></i>
	<span class="max-sm:hidden ml-1">Followed</span>
	@else
	<i class="fa-solid fa-bell"></i>
	<span class="max-sm:hidden ml-1">Follow</span>
	@endif
</button>