@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6">
    <h1 class="text-2xl font-bold">Search Results</h1>
    <p>Showing results for: <strong>{{ $query }}</strong></p>

    @if(count($results) === 0)
        <p>No results found.</p>
    @else
        <ul class="mt-4 space-y-3">
            @foreach($results as $item)
                <li class="p-4 bg-gray-100 rounded-lg shadow">
                    <h2 class="text-xl font-semibold">{{ $item->name }}</h2>
                    <p>{{ $item->description }}</p>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection