<article class="w-full bg-white shadow-md space-x-6 rounded-lg mb-4" data-id="{{ $notification->id }}">
    <div class="flex flex-col p-4">
        <div class="flex flex-row justify-between items-center text-gray-500 text-sm mb-1">
            <span>{{ $notification->sent_at->diffForHumans() }}</span>
       
            <button class="text-red-500 hover:text-red-700 focus:outline-none delete-notification" data-id="{{ $notification->id }}" title="Delete Notification">
                <i class="fa-solid fa-xmark"></i> 
            </button>
        </div>

        <h5 class="text-lg font-semibold break-words">
            {{ $notification->description }}
        </h5>
    </div>
</article>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-notification').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.getAttribute('data-id');

            
                fetch(`/notifications/${notificationId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                })
                .then(response => response.json()) 
                .then(data => {
                    if (data.success) {
                     
                        const notificationCard = this.closest('article');
                        notificationCard.remove();
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                })
                .catch(error => {
                    alert('Error deleting notification');
                    console.error(error);
                });
            });
        });
    });
</script>