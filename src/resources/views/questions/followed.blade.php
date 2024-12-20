@extends('layouts.app')

@section('title', 'Followed Questions')

@php
    $crumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Followed Questions', 'url' => route('followed-questions')]
    ];
@endphp

@section('content')
<div class="flex flex-row flex-grow">
    
    @include('partials.sidebar')
   
    <div class="container mx-auto p-4">
        {!! breadcrumbs($crumbs) !!}
        <h2 class="text-2xl font-semibold mb-4">
            Followed Questions
            @include('partials.tip', ['tip' => "Here are the questions you follow."])
        <h2
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
