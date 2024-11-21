@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    <section class="bg-gray-100 px-4">
        @include('partials.sidebar')
    </section>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">My Questions</h2>
        @if($myQuestions->isEmpty())
        <p class="text-gray-700">You have not created any questions.</p>
        @else
        <div class="space-y-4">
            @foreach($myQuestions as $question)
                @include('partials.question-card', $question)
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection