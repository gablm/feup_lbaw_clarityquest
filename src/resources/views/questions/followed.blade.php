@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    @include('partials.sidebar')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">
            Followed Questions
            @include('partials.tip', ['tip' => "Here are the questions you follow."])
        @if($followedQuestions->isEmpty())
            <p class="text-gray-700">You are not following any questions.</p>
        @else
            <div class="space-y-4">
                @foreach($followedQuestions as $question)
                    @include('partials.question-card', $question)
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection