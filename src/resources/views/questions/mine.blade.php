@extends('layouts.app')

@section('content')
<div class="flex flex-row">
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
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h3 class="text-xl font-semibold mb-2">{{ $question->title }}</h3>
                    <p class="text-gray-700 mb-2">{{ $question->post->text }}</p>
                    <div class="text-gray-500 text-sm mb-2">
                        <span>Score: {{ $question->post->votes }}</span> |
                        <span>Upvotes: {{ $question->post->positiveVotes() }}</span> |
                        <span>Downvotes: {{ $question->post->negativeVotes() }}</span>
                    </div>
                    <div class="text-gray-500 text-sm">
                        <span>Asked by {{ $question->post->user->name }}</span> |
                        <span>{{ $question->answers->count() }} Answers</span> |
                        <span>{{ $question->post->comments->count() }} Comments</span>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection