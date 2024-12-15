@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Search Results</h2>
        <p class="text-gray-700">Showing results for: <strong>{{ $query }}</strong></p>

        <!-- Sorting and Filtering Options -->
        <div class="flex justify-between items-center mb-4">
            <div>
                <form method="GET" action="{{ route('search') }}" class="flex space-x-2">
                    <input type="hidden" name="search" value="{{ $query }}">

                    <!-- Filter Dropdown -->
                    <select name="filter" class="border rounded px-2 py-1">
                        <option value="all" {{ request('filter') === 'all' ? 'selected' : '' }}>All</option>
                        <option value="questions" {{ request('filter') === 'questions' ? 'selected' : '' }}>Questions</option>
                        <option value="users" {{ request('filter') === 'users' ? 'selected' : '' }}>Users</option>
                        <option value="tags" {{ request('filter') === 'tags' ? 'selected' : '' }}>Tags</option>
                    </select>

                    <!-- Sort Dropdown -->
                    <select name="sort" class="border rounded px-2 py-1">
                        <option value="relevance" {{ request('sort') === 'relevance' ? 'selected' : '' }}>Relevance</option>
                        <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                        <option value="alphabetical" {{ request('sort') === 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                    </select>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Apply</button>
                </form>
            </div>
        </div>

        @if($questions->isEmpty() && $users->isEmpty() && $tags->isEmpty())
            <p class="text-gray-700 mt-4">No results found.</p>
        @endif

        @if(!$questions->isEmpty())
            <h2 class="text-md font-semibold my-4">Questions</h2>
            <div class="space-y-4 mt-4">
                @foreach($questions as $question)
                    @include('partials.question-card', $question)
                @endforeach
            </div>
        @endif

        @if(!$users->isEmpty())
            <h2 class="text-md font-semibold my-4">Users</h2>
            <div class="space-y-4 mt-4">
                @foreach($users as $user)
                    @include('partials.user-card', ['user' => $user, 'panel' => false])
                @endforeach
            </div>
        @endif

        @if(!$tags->isEmpty())
            <h2 class="text-md font-semibold my-4">Tags</h2>
            <div class="space-y-4 mt-4">
                @foreach($tags as $tag)
                    @include('partials.tag-card', ['tag' => $tag, 'panel' => false])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection