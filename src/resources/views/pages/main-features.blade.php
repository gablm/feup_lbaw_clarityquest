@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-semibold mb-4">Main Features</h1>

    <div class="feature mb-4">
        <h2 class="text-2xl font-semibold">Feature 1: Question and Answer System</h2>
        <p class="text-gray-700">Users can ask questions and provide answers to others, creating a collaborative knowledge base.</p>
    </div>

    <div class="feature mb-4">
        <h2 class="text-2xl font-semibold">Feature 2: Voting System</h2>
        <p class="text-gray-700">Users can vote on questions and answers to highlight the most useful content.</p>
    </div>

    <div class="feature mb-4">
        <h2 class="text-2xl font-semibold">Feature 3: Tagging System</h2>
        <p class="text-gray-700">Questions can be tagged with relevant keywords, making it easier to categorize and search for content.</p>
    </div>

    <div class="feature mb-4">
        <h2 class="text-2xl font-semibold">Feature 4: User Profiles</h2>
        <p class="text-gray-700">Each user has a profile page where they can update their information, view their activity, and manage their settings.</p>
    </div>

    <div class="feature mb-4">
        <h2 class="text-2xl font-semibold">Feature 5: Real-time Notifications</h2>
        <p class="text-gray-700">Users receive real-time notifications for important events, such as new answers to their questions or comments on their posts.</p>
    </div>
</div>
@endsection