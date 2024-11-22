@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Search Results</h2>
        <p class="text-gray-700">Showing results for: <strong>{{ $query }}</strong></p>

        @if($results->isEmpty())
        <p class="text-gray-700 mt-4">No results found.</p>
        @else
        <div class="space-y-4 mt-4"> <!-- Vertical list format -->
            @foreach($results as $question)
                @include('partials.question-card', $question)
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection