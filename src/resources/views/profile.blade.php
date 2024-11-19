@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Profile</h2>
        <div class="mb-4">
            <h3 class="text-xl font-semibold">User Information</h3>
            <p><strong>Username:</strong> {{ Auth::user()->username }}</p>
            <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
        </div>
        <div class="mb-4">
            <h3 class="text-xl font-semibold">Activity</h3>
            <!-- Add user activity details here -->
        </div>
        <div class="mt-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</div>
@endsection