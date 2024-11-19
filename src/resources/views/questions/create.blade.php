@extends('layouts.app')

@section('content')
<div class="flex flex-row">
	<section class="bg-gray-100 px-4">
		@include('partials.sidebar')
	</section>
	<div class="container mx-auto p-4">
		<div class="bg-white shadow-md rounded-lg p-6">
			<h2 class="text-2xl font-semibold mb-4">Create Question</h2>
			<form method="POST" action="{{ route('questions-create') }}">
				{{ csrf_field() }}

				<div class="mb-4">
					<label class="auth" for="title">Title</label>
					<input class="auth focus:outline-none focus:shadow-outline" id="title" type="text" name="title" value="{{ old('title') }}" required>
					@if ($errors->has('title'))
					<span class="auth-error bold">
						{{ $errors->first('title') }}
					</span>
					@endif
				</div>

				<div class="mb-4">
					<label class="auth" for="description">Description</label>
					<textarea class="auth focus:outline-none focus:shadow-outline" cols="50" rows="10" id="description" type="textarea" name="description" value="{{ old('description') }}" required></textarea>
					@if ($errors->has('description'))
					<span class="auth-error bold">
						{{ $errors->first('description') }}
					</span>
					@endif
				</div>

				<div class="flex items-center justify-between">
					<button class="auth-main focus:outline-none focus:shadow-outline" type="submit">
						Create
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection