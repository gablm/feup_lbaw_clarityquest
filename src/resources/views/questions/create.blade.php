@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    @include('partials.sidebar')
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-4">
            Create Question
            @include('partials.tip', ['tip' => "Through this form you can upload a question and add a tag to it."])
        </h2>
        <form method="POST" action="{{ route('questions-create') }}">
            {{ csrf_field() }}

            <div class="mb-4">
                <label class="auth" for="title">Title</label>
                <input class="auth focus:outline-none focus:shadow-outline" id="title" type="text" name="title" value="{{ old('title') }}" required placeholder="Enter the title">
                @if ($errors->has('title'))
                <span class="auth-error bold">
                    {{ $errors->first('title') }}
                </span>
                @endif
            </div>

            <div class="mb-4">
                <label class="auth" for="description">Description</label>
                <textarea class="auth focus:outline-none focus:shadow-outline resize-none" cols="50" rows="10" id="description" type="textarea" name="description" required placeholder="Enter the description">{{ old('description') }}</textarea>
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

            <div class="flex items-center justify-between">
                <button class="auth-main focus:outline-none focus:shadow-outline" type="submit">
                    Create
                </button>
            </div>
        </form>
    </div>
</div>
@endsection