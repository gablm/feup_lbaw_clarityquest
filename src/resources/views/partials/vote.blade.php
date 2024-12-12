<div id="vote-status" class="space-x-1">
    <button onclick="sendVoteRequest({{ $id }}, true)" class="vote-link fa-solid fa-up-long hover:text-red-600 {{ $voteStatus === 'positive' ? 'text-red-600' : '' }}"></button>
    <span id="votes-{{ $id }}" class="vote-count">{{ $votes }}</span>
    <button onclick="sendVoteRequest({{ $id }}, false)" class="vote-link fa-solid fa-down-long hover:text-blue-500 {{ $voteStatus === 'negative' ? 'text-blue-500' : '' }}"></button>
</div>