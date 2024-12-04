<div class="container mx-auto p-4">
    <div class="profile-header">
        <h1>Your Medals</h1>
    </div>

    <div class="medals-section">
        <h2>Medals</h2>
        @if ($medals)
            <p>Posts Upvoted: {{ $medals->posts_upvoted }}</p>
            <p>Posts Created: {{ $medals->posts_created }}</p>
            <p>Questions Created: {{ $medals->questions_created }}</p>
            <p>Answers Posted: {{ $medals->answers_posted }}</p>
            <p>Total Medals: {{ $user->totalMedals() }}</p>
        @else
            <p>No medals available.</p>
        @endif
    </div>
</div>
