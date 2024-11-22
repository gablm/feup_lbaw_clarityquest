@extends('layouts.app')

@section('content')
<div class="flex flex-row">

    <section class="bg-gray-100 px-4">
        @include('partials.sidebar')
    </section>

    <div class="container mx-auto col-span-9 p-4">
        <h1 class="text-2xl font-bold">Search Results</h1>
        <p>Showing results for: <strong>{{ $query }}</strong></p>

        @if($results->isEmpty())
            <p class="text-gray-700 mt-4">No results found.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mt-4">
                @foreach($results as $question)
                    @include('partials.question', ['question' => $question, 'mine' => false, 'details' => true])
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection