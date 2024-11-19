@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-semibold mb-4">My Answers</h2>
    @if($answers->isEmpty())
        <p class="text-gray-700">You have not posted any answers.</p>
    @else
        <div class="space-y-4">
            @foreach($answers as $answer)
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h3 class="text-xl font-semibold mb-2">Question: {{ $answer->question->title }}</h3>
                    <p class="text-gray-700 mb-2">{{ $answer->text }}</p>
                    <div class="text-gray-500 text-sm">
                        <span>Answered on {{ $answer->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection