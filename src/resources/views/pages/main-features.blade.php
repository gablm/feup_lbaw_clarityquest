@extends('layouts.app')

@section('title', 'Main Features')

@php
    $crumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Main Features', 'url' => route('main-features')]
    ];
@endphp

@section('content')
<div class="container mx-auto p-4">
    {!! breadcrumbs($crumbs) !!}
    <h1 class="text-4xl font-bold mb-8 text-center">Main Features</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div class="feature p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">Feature 1: Question and Answer System</h2>
            <p class="text-gray-700">Users can ask questions and provide answers to others, creating a collaborative knowledge base.</p>
        </div>

        <div class="feature p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">Feature 2: Voting System</h2>
            <p class="text-gray-700">Users can vote on questions and answers to highlight the most useful content.</p>
        </div>

        <div class="feature p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">Feature 3: Tagging System</h2>
            <p class="text-gray-700">Questions can be tagged with relevant keywords, making it easier to categorize and search for content.</p>
        </div>

        <div class="feature p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">Feature 4: User Profiles</h2>
            <p class="text-gray-700">Each user has a profile page where they can update their information, view their activity, and manage their settings.</p>
        </div>

        <div class="feature p-6 bg-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-semibold mb-2">Feature 5: Real-time Notifications</h2>
            <p class="text-gray-700">Users receive real-time notifications for important events, such as new answers to their questions or comments on their posts.</p>
        </div>
    </div>
</div>
@endsection