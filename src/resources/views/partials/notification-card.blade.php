<article class="w-full bg-white shadow-md space-x-6 rounded-lg mb-4" data-id="{{ $notification->id }}">
    <div class="flex flex-col p-4">
    
        <div class="flex flex-row justify-between space-x-6 items-center text-gray-500 text-sm mb-1">
            <span>{{ $notification->sent_at->diffForHumans() }}</span>
        </div>
        
        <h5 class="text-lg font-semibold break-words">
            {{ $notification->description }}
        </h5>

        <div class="flex flex-row justify-between space-x-6 text-gray-500 text-sm mt-2">
            <div>
                <span class="{{ $notification->read ? 'text-gray-400' : 'text-blue-500 font-semibold' }}">
                    {{ $notification->read ? 'Read' : 'Unread' }}
                </span>
            </div>
            <a href="{{ route('notifications.read', $notification->id) }}" class="text-blue-500 hover:underline">
                Mark as Read
            </a>
        </div>
    </div>
</article>