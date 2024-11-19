@extends('layouts.app')

@section('content')
<div class="flex flex-row">
	<section class="bg-gray-100 px-4">
		@include('partials.sidebar')
	</section>
	<div class="container mx-auto col-span-9 p-4">
		<div class="mb-4">
            <h3 class="text-xl font-semibold mb-2">Top Questions</h3>
            @if($topQuestions->isEmpty())
                <p class="text-gray-700">No top questions available.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($topQuestions as $question)
						@include('partials.question', ['question' => $question, 'mine' => false, 'details' => true])
                    @endforeach
                </div>
            @endif
        </div>

		<div class="mb-4">
            <h3 class="text-xl font-semibold mb-2">Latest Questions</h3>
            @if($latestQuestions->isEmpty())
                <p class="text-gray-700">No latest questions available.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($latestQuestions as $question)
						@include('partials.question', ['question' => $question, 'mine' => false, 'details' => true])
                    @endforeach
                </div>
            @endif
        </div>
	</div>
</div>
@endsection