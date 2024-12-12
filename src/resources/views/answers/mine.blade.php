@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
	@include('partials.sidebar')
	<div class="container mx-auto p-4">
		<h2 class="text-2xl font-semibold mb-4">
            My Answers
			@include('partials.tip', ['tip' => "Here are the answers you have added."])
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