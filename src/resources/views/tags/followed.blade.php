@extends('layouts.app')

@section('title')
    Followed Tags
@endsection

@section('content')
@php
    $crumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Followed Tags', 'url' => route('followed-tags')],
    ];
@endphp
<div class="flex flex-row flex-grow">
	
	@include('partials.sidebar')
	<div class="container mx-auto p-4">
		{!! breadcrumbs($crumbs) !!}
		<h2 class="text-2xl font-semibold mb-4">
            Followed Tags
            @include('partials.tip', ['tip' => "Here are the tags you follow."])
        </h2>
		@if($followedTags->isEmpty())
		<p class="text-gray-700">You are not following any tags.</p>
		@else
		<div class="flex flex-col space-y-4">
			@foreach($followedTags as $tag)
			@include('partials.tag-card', ['tag' => $tag, 'panel' => false])
			@endforeach
		</div>
		@endif
	</div>
</div>
@endsection
