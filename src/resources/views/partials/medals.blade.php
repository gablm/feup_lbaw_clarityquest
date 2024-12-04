<div class="container mx-auto p-4">
    <div class="medals-section">
        @if ($medals)
            <article class="w-full bg-white shadow-md rounded-lg mb-4">
                <div class="grid grid-cols-4 p-4">
                    <div class="flex items-center justify-center col-span-1">
                        <img
                            src="{{ url('img/medal.svg') }}"
                            alt="Medal Icon"
                            class="w-32 h-auto object-cover">
                    </div>
                    <div class="col-span-2 text-gray-700 mb-3 mt-1 break-words">
                        <p class="text-gray-700"><b> Posts Upvoted: </b>{{ $medals->posts_upvoted }}</p>
                        <p class="text-gray-700"><b> Posts Created: </b>{{ $medals->posts_created }}</p>
                        <p class="text-gray-700"><b> Questions Created: </b>{{ $medals->questions_created }}</p>
                        <p class="text-gray-700"><b> Answers Posted: </b>{{ $medals->answers_posted }}</p>
                        <p class="text-black"><b> Total: </b>{{ $user->totalMedals() }}</p>
                    </div>
                </div>
            </article>
        @else
            <p class="text-gray-700">No medals available.</p>
        @endif
    </div>
</div>