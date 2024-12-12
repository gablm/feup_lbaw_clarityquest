<div id="vote-status" class="space-x-1">
    <button onclick="sendVoteRequest({{ $id }}, true)" class="vote-link fa-solid fa-up-long hover:text-green-600 {{ $voteStatus === 'positive' ? 'text-green-600' : '' }}"></button>
    <span id="votes-{{ $id }}" class="vote-count">{{ $votes }}</span>
    <button onclick="sendVoteRequest({{ $id }}, false)" class="vote-link fa-solid fa-down-long hover:text-red-500 {{ $voteStatus === 'negative' ? 'text-red-500' : '' }}"></button>
</div>