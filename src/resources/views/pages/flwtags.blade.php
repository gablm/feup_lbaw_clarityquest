@extends('layouts.app')

@section('content')
<div class="flex flex-row">
    <section class="bg-gray-100 px-4">
        @include('partials.sidebar')
    </section>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Followed Tags</h2>
        @if($followedTags->isEmpty())
            <p class="text-gray-700">You are not following any tags.</p>
        @else
            <div class="space-y-4">
                @foreach($followedTags as $tag)
                    <div class="bg-white shadow-md rounded-lg p-4">
                        <h3 class="text-xl font-semibold mb-2">{{ $tag->name }}</h3>
                        <div class="text-gray-500 text-sm">
                            <span>{{ $tag->posts->count() }} Posts</span>
                        </div>
                        <div class="mt-2">
                            @foreach($tag->posts as $post)
                                <div class="bg-gray-100 p-2 rounded mb-2">
                                    <h4 class="text-lg font-semibold">{{ $post->title }}</h4>
                                    <p class="text-gray-700">{{ $post->text }}</p>
                                    <div class="text-gray-500 text-sm mb-2">
                                        <span>Total Votes: {{ $post->votes }}</span> |
                                        <span>Positive Votes: {{ $post->positiveVotes() }}</span> |
                                        <span>Negative Votes: {{ $post->negativeVotes() }}</span>
                                    </div>
                                    <div class="text-gray-500 text-sm">
                                        <span>By {{ $post->user->name }}</span> |
                                        <span>{{ $post->comments->count() }} Comments</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection