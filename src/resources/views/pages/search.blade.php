@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Search Results</h2>
        
        <div class="flex justify-between items-center mb-4">
            <p class="text-gray-700">Showing results for: <strong>{{ $query }}</strong></p>

            <div class="flex space-x-2">
                <form method="GET" action="{{ route('search') }}" class="flex space-x-2">
                    <input type="hidden" aria-label="Search" name="search" value="{{ $query }}">

                    <!-- Filter Dropdown -->
                    <div class="flex flex-col">
                        <label for="filter" class="font-bold text-gray-700 mb-1">Filter by</label>
                        <select name="filter" id="filter" class="border rounded px-2 py-1">
                            <option value="all" {{ request('filter') === 'all' ? 'selected' : '' }}>All</option>
                            <option value="questions" {{ request('filter') === 'questions' ? 'selected' : '' }}>Questions</option>
                            <option value="users" {{ request('filter') === 'users' ? 'selected' : '' }}>Users</option>
                            <option value="tags" {{ request('filter') === 'tags' ? 'selected' : '' }}>Tags</option>
                        </select>
                    </div>

                    <!-- Sort Dropdown -->
                    <div class="flex flex-col">
                        <label for="sort" class="font-bold text-gray-700 mb-1">Sort by</label>
                        <select id="sort" name="sort" class="border rounded px-2 py-1">
                            <option value="none" {{ request('sort') === 'none' ? 'selected' : '' }}>None</option>
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="alphabetical" {{ request('sort') === 'alphabetical' ? 'selected' : '' }}>Alphabetical</option>
                            <option value="most_upvoted" {{ request('sort') === 'most_upvoted' ? 'selected' : '' }}>Most Upvoted</option>
                            <option value="least_upvoted" {{ request('sort') === 'least_upvoted' ? 'selected' : '' }}>Least Upvoted</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" aria-label="Filter and Sort Search results" class="bg-blue-500 text-white px-4 py-1 rounded">Apply</button>
                    </div>
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