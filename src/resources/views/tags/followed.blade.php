@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
	@include('partials.sidebar')
	<div class="container mx-auto p-4">
		<h2 class="text-2xl font-semibold mb-4">Followed Tags</h2>
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