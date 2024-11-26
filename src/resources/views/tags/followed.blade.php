@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
	@include('partials.sidebar')
	<div class="container mx-auto p-4">
		<h2 class="text-2xl font-semibold mb-4">
            My Tags
            <span class="text-sm text-gray-500 relative group">[?]
                <span class="absolute hidden group-hover:block bg-gray-200 text-black text-sm rounded py-2 px-6 left-full ml-2 tooltiptext">
                    Here are the tags you follow.
                </span>
            </span>
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