@if (Auth::check())
<div id="vote-status-{{ $id }}" class="flex items-center space-x-1">
    <button title="Upvote" onclick="sendVoteRequest({{ $id }}, true)" class="vote-link flex items-center {{ $voteStatus === 'positive' ? 'text-green-600' : 'hover:text-green-600' }}">
		<i class="fa-solid fa-up-long"></i>
	</button>
    <span id="votes-{{ $id }}" class="vote-count">{{ $votes }}</span>
    <button title="Downvote" onclick="sendVoteRequest({{ $id }}, false)" class="vote-link flex items-center {{ $voteStatus === 'negative' ? 'text-red-500' : 'hover:text-red-500' }}">
		<i class="fa-solid fa-down-long"></i>
	</button>
</div>
@else
<div id="vote-status-{{ $id }}" class="space-x-1">
    <a class="vote-link fa-solid fa-up-long hover:text-green-600"></a>
    <span id="votes-{{ $id }}" class="vote-count">{{ $votes }}</span>
    <a class="vote-link fa-solid fa-down-long hover:text-red-500"></a>
</div>	
@endif