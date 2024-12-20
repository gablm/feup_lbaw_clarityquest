@extends('layouts.app')

@section('title', 'Create Question')

@section('content')
<div class="flex flex-row flex-grow">
	@include('partials.sidebar')
	<div class="container mx-auto p-4">
		<form method="POST" action="{{ route('questions-create') }}">
			{{ csrf_field() }}

			<fieldset class="my-4">
				<legend class="text-2xl font-semibold mb-4">Create Question</legend>

				<div class="flex flex-col mb-4">
					<label class="auth" for="title">Title<span class="font-bold text-red-600">*</span></label>
					<input onkeyup="charCounter(this, this, 250)" onkeydown="charCounter(this, this, 250)"
						class="auth focus:outline-none focus:shadow-outline" id="title" type="text" name="title"
						value="{{ old('title') }}" minlength="1" maxlength="250" required placeholder="Enter the title">
					<span class="counter mt-2">{{ strlen(old('title')) }}/250 characters</span>
					@if ($errors->has('title'))
						<span class="auth-error bold">
							{{ $errors->first('title') }}
						</span>
					@endif
				</div>

				<div class="mb-4">
					<label class="auth" for="description">Description<span class="font-bold text-red-600">*</span></label>
					<textarea onkeyup="charCounter(this, this, 3000)" onkeydown="charCounter(this, this, 3000)"
						class="auth focus:outline-none focus:shadow-outline resize-none" cols="50" rows="10"
						id="description" type="textarea" name="description" required placeholder="Enter the description"
						minlength="1" maxlength="3000">{{ old('description') }}</textarea>
					<span class="counter mt-2">{{ strlen(old('description')) }}/3000 characters</span>
					@if ($errors->has('description'))
						<span class="auth-error bold">
							{{ $errors->first('description') }}
						</span>
					@endif
				</div>

				<div class="mb-4">
					<label class="auth" for="tags">Tags</label>
					<select name="tags[]" id="tags" class="auth focus:outline-none focus:shadow-outline" multiple>
						<option selected value="">None</option>
						@foreach ($tags as $tag)
							<option value={{ $tag->id }}>{{ $tag->name }}</option>
						@endforeach
					</select>
					@if ($errors->has('tags'))
						<span class="auth-error bold">
							{{ $errors->first('tags') }}
						</span>
					@endif
				</div>
			</fieldset>

			<div class="flex items-center justify-between">
				<button aria-label="Create Question" class="auth-main focus:outline-none focus:shadow-outline"
					type="submit">
					Create
				</button>
			</div>
		</form>
	</div>
</div>
@endsection