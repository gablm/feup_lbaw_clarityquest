@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Profile</h2>
            <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                Edit Profile
            </a>
        </div>
        <div class="mb-4">
            <h3 class="text-xl font-semibold">User Information</h3>
            <div class="flex items-center mb-4">
                @if (Auth::user()->profile_pic)
                    <img src="{{ asset(Auth::user()->profile_pic) }}" alt="Profile Picture" class="h-16 w-16 rounded-full mr-4 object-cover">
                @else
                    <img src="{{ url('img/default_pic.png') }}" alt="Default Profile Picture" class="h-16 w-16 rounded-full mr-4 object-cover">
                @endif
                <div>
                    <p><strong>Username:</strong> {{ Auth::user()->username }}</p>
                    <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                    <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                </div>
            </div>
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
    <div class="mt-4">
        <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-2 py-1 text-sm bg-red-500 text-white rounded-md hover:bg-red-600">
                Delete Account
            </button>
        </form>
    </div>
</div>
@endsection