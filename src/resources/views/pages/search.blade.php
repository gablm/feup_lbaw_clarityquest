@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Search Results</h2>
        <p class="text-gray-700">Showing results for: <strong>{{ $query }}</strong></p>
		<h2 class="text-md font-semibold my-4">Questions</h2>
        @if($questions->isEmpty())
        <p class="text-gray-700 mt-4">No results found.</p>
        @else
        <div class="space-y-4 mt-4">
            @foreach($questions as $question)
                @include('partials.question-card', $question)
            @endforeach
        </div>
		@endif
		<h2 class="text-md font-semibold my-4">Users</h2>
		@if($users->isEmpty())
        <p class="text-gray-700 mt-4">No results found.</p>
		@else
		<div class="space-y-4 mt-4">
            @foreach($users as $user)
			@include('partials.user-card', ['user' => $user, 'panel' => false])
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection