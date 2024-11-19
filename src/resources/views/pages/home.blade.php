@extends('layouts.app')

@section('content')
<div class="flex flex-row">
	<section class="bg-gray-100 px-4">
		@include('partials.sidebar')
	</section>
	<div class="container mx-auto col-span-9 p-4">
		<div class="mb-4">
			<h3 class="text-xl font-semibold mb-2">Top</h3>
			<div class="bg-white shadow-md rounded-lg mb-3">
				<div class="p-4">
					<h5 class="text-lg font-semibold">Example Question Title</h5>
					<p class="text-gray-700">This is a sample description for the top question.</p>
					<small class="text-gray-500">By John Doe | 0 Answers | 0 Comments</small>
				</div>
			</div>
		</div>

		<div class="mb-4">
			<h3 class="text-xl font-semibold mb-2">Following</h3>
			<!-- Add content for following section here -->
		</div>

		<div>
			<h3 class="text-xl font-semibold mb-2">Latest</h3>
			<!-- Add content for latest section here -->
		</div>
	</div>
</div>
@endsection