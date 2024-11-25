<button onclick="blockUser({{ $user->id }})" class="tool-link">
	@if ($user->isBlocked())
	<i class="fa-solid fa-unlock"></i>
	<span class="max-md:hidden ml-1">Unblock</span>
	@else
	<i class="fa-solid fa-shield-halved"></i>
	<span class="max-md:hidden ml-1">Block</span>
	@endif
</button>