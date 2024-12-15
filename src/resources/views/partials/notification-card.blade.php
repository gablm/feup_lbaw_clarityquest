@php
$post = $notification->posts()->first();
@endphp

<article id="notification" class="w-full bg-white shadow-md space-x-6 rounded-lg mb-4" data-id="{{ $notification->id }}">
    @if ($post)
	<a href="{{ url('/questions/' . $post->id ) }}">
	@endif
	<div class="flex flex-col p-4">
        <div class="flex flex-row justify-between items-center text-gray-500 text-sm mb-1">
            <span>{{ $notification->sent_at->diffForHumans() }}</span>
            <button class="tool-link text-red-500" onclick="deleteNotification({{ $notification->id }})" title="Delete Notification">
                <i class="fa-solid fa-xmark"></i> 
            </button>
        </div>
        <h5 class="text-lg font-semibold break-words">
            {{ $notification->description }}
        </h5>
    </div>
	@if ($post)
	</a>
	@endif
</article>
