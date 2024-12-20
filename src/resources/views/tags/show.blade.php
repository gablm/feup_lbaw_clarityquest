@extends('layouts.app')

@php
$questions = $tag->questions;
@endphp

@section('title', "Tag - #{$tag->name}")

@section('content')
<div class="flex flex-row flex-grow">
    @include('partials.sidebar')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">
			Tag
			<a class="tag-big">{{ $tag->name }}</a>
			@if (Auth::check())
            @include('partials.follow-tag-btn', $tag)
			@endif
		</h2>
        @if($tag->questions->isEmpty())
        <p class="text-gray-700">There are no questions under this topic.</p>
        @else
        <div class="space-y-4">
            @foreach($tag->questions as $question)
                @include('partials.question-card', $question)
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection