<div class="container mx-auto p-4">
    <div class="profile-header">
        <h1>Your Medals</h1>
    </div>

    <div class="medals-section">
        @if ($medals)
            <article class="w-full bg-white shadow-md rounded-lg mb-4">
                <div class="flex flex-col p-4">
                    <div class="flex flex-row items-center text-gray-500 text-sm mb-1">
                        <img
                            src="{{ url('img/medal.svg') }}"
                            alt="Medal Icon"
                            class="w-12 h-12 object-cover mr-4">
                        <span class="text-lg font-semibold">Medals</span>
                    </div>
                    <div class="text-gray-700 mb-3 mt-1 break-words ml-16">
                        <p>Posts Upvoted: {{ $medals->posts_upvoted }}</p>
                        <p>Posts Created: {{ $medals->posts_created }}</p>
                        <p>Questions Created: {{ $medals->questions_created }}</p>
                        <p>Answers Posted: {{ $medals->answers_posted }}</p>
                        <p>Total Medals: {{ $user->totalMedals() }}</p>
                    </div>
                </div>
            </article>
        @else
            <p class="text-gray-700">No medals available.</p>
        @endif
    </div>
</div>