@extends('layouts.app')

@section('content')
<div class="flex flex-row">
    <section class="bg-gray-100 px-4">
        @include('partials.sidebar')
    </section>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">My Answers</h2>
        @if($answers->isEmpty())
            <p class="text-gray-700">You have not posted any answers.</p>
        @else
            <div class="space-y-4">
                @foreach($answers as $answer)
                    @include('partials.answer-card', $answer)
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection