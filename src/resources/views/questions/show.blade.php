@extends('layouts.app')

@section('title', $question->title)

@section('content')
<div class="flex flex-row">
	<section class="bg-gray-100 px-4">
		@include('partials.sidebar')
	</section>
	<div class="container mx-auto p-4">
		@include('partials.question', $question)
	</div>
</div>
@endsection