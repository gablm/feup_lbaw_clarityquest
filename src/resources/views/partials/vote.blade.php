@if (Auth::check())
<div id="vote-status-{{ $id }}" class="space-x-1">
    <button onclick="sendVoteRequest({{ $id }}, true)" class="vote-link fa-solid fa-up-long {{ $voteStatus === 'positive' ? 'text-green-600' : 'hover:text-green-600' }}"></button>
    <span id="votes-{{ $id }}" class="vote-count">{{ $votes }}</span>
    <button onclick="sendVoteRequest({{ $id }}, false)" class="vote-link fa-solid fa-down-long {{ $voteStatus === 'negative' ? 'text-red-500' : 'hover:text-red-500' }}"></button>
</div>
@else
<div id="vote-status-{{ $id }}" class="space-x-1">
    <a class="vote-link fa-solid fa-up-long hover:text-green-600"></a>
    <span id="votes-{{ $id }}" class="vote-count">{{ $votes }}</span>
    <a class="vote-link fa-solid fa-down-long hover:text-red-500"></a>
</div>	
@endif