@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700">Name</label>
                <input type="text" name="name" id="name" value="{{ Auth::user()->name }}" class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="{{ Auth::user()->email }}" class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="mb-4">
                <label for="profile_pic" class="block text-gray-700">Profile Picture</label>
                <input type="file" name="profile_pic" id="profile_pic" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-3 py-2 border rounded-md">
                @if (Auth::user()->profile_pic)
                    <div class="mt-2">
                        <input type="checkbox" name="remove_profile_pic" id="remove_profile_pic" value="1">
                        <label for="remove_profile_pic" class="text-gray-700">Remove current profile picture</label>
                    </div>
                @endif
            </div>

            <div class="mb-4">
                <label for="bio" class="block text-gray-700">Bio</label>
                <textarea name="bio" id="bio" class="w-full px-3 py-2 border rounded-md">{{ Auth::user()->bio }}</textarea>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700">New Password</label>
                <input type="password" name="password" id="password" class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-3 py-2 border rounded-md">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection