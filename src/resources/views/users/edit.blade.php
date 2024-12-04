@extends('layouts.app')

@php
$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');
@endphp

@section('content')
<div class="container mx-auto p-4">
	<div class="bg-white shadow-md rounded-lg p-6">
		@if (Auth::user()->id == $user->id)
		<h2 class="text-2xl font-semibold mb-4">
            Edit Profile
            <span class="text-sm text-gray-500 relative group">[?]
                <span class="absolute hidden group-hover:block bg-gray-200 text-black text-sm rounded py-2 px-6 left-full ml-2 tooltiptext">
                    You can edit your profile fields through this form.
                </span>
            </span>
        </h2>
		@else
		<div class="flex space-x-2">
		<h2 class="text-2xl font-semibold mb-4">Edit Profile</h2>
			<a class="tool-link" href="{{ $user ? url('/users/' . $user->id) : '/' }}">
				<div class="flex flex-row items-center">
					<img
						src="{{ $profile_pic }}"
						alt="Profile Picture"
						class="w-6 h-6 rounded-full object-cover">
					<span class="ml-2">{{ $user->name ?? "[REDACTED]" }}</span>
				</div>
			</a>
		</div>
		@endif
		
		<form method="POST" action="{{ url('/users/' . $user->id) }}" enctype="multipart/form-data">
			@csrf
			@method('PATCH')

			@if ($errors->any())
			<div class="mb-4">
				<ul class="list-disc list-inside text-red-500">
					@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
			@endif

			<div class="mb-4">
				<label for="name" class="block text-gray-700">Name</label>
				<input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" placeholder="Enter your name" class="w-full px-3 py-2 border rounded-md">
			</div>

			<div class="mb-4">
				<label for="email" class="block text-gray-700">Email</label>
				<input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" placeholder="Enter your email" class="w-full px-3 py-2 border rounded-md">
			</div>

			<div class="mb-4">
				<label for="profile_pic" class="block text-gray-700">Profile Picture</label>
				<input type="file" name="profile_pic" id="profile_pic" accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full px-3 py-2 border rounded-md">
				@if ($user->profile_pic)
				<div class="mt-2">
					<input type="checkbox" name="remove_profile_pic" id="remove_profile_pic" value="1">
					<label for="remove_profile_pic" class="text-gray-700">Remove current profile picture</label>
				</div>
				@endif
			</div>

			<div class="mb-4">
				<label for="bio" class="block text-gray-700">Bio</label>
				<textarea name="bio" id="bio" placeholder="Tell others about yourself" class="w-full px-3 py-2 border rounded-md">{{ old('bio', $user->bio) }}</textarea>
			</div>

			<div class="mb-4">
				<label for="password" class="block text-gray-700">New Password</label>
				<input type="password" name="password" id="password" placeholder="Enter a new password" class="w-full px-3 py-2 border rounded-md">
			</div>

			<div class="mb-4">
				<label for="password_confirmation" class="block text-gray-700">Confirm New Password</label>
				<input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm your new password" class="w-full px-3 py-2 border rounded-md">
			</div>

			@if (Auth::user()->isAdmin())
			<div class="mb-4">
                <label class="auth" for="role">Role</label>
                <select name="role" id="role" class="auth focus:outline-none focus:shadow-outline">
                    @foreach (\App\Enum\User\Permission::cases() as $role)
                    <option value="{{ $role->value }}" @if ($role == $user->role) selected @endif>{{ $role->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('role'))
                <span class="auth-error bold">
                    {{ $errors->first('role') }}
                </span>
                @endif
            </div>
			@endif

			<div class="flex justify-end">
				<button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update Profile</button>
			</div>
		</form>
	</div>
</div>
@endsection