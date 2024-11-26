@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
	@include('partials.sidebar')
	<div class="container mx-auto p-4">
		<h2 class="text-2xl font-semibold mb-4">
            My Answers
            <span class="text-sm text-gray-500 relative group">[?]
                <span class="absolute hidden group-hover:block bg-gray-200 text-black text-sm rounded py-2 px-6 left-full ml-2 tooltiptext">
                    Here are the answers you have added.
                </span>
            </span>
        </h2>
		@if($answers->isEmpty())
		<p class="text-gray-700">You have not posted any answers.</p>
		@else
		<div class="flex flex-col space-y-4">
			@foreach($answers as $answer)
			@include('partials.answer-card', $answer)
			@endforeach
		</div>
		@endif
	</div>
</div>
@endsection