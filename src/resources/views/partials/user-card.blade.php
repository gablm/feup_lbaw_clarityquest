@php
$profile_pic = $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');
@endphp

<article id="user" class="w-full bg-white shadow-md space-x-4 rounded-lg" data-id="{{ $user->id }}">
	<div class="flex flex-row justify-between items-center p-4">
		<a href={{ url('/users/' . $user->id) }}>
			<div class="flex flex-row space-x-3 text-gray-500 text-sm mb-1">
				<div class="max-md:hidden flex flex-row items-center">
					<img
						src="{{ $profile_pic }}"
						alt="Profile Picture"
						class="w-10 h-10 rounded-full object-cover">
				</div>
				<div class="flex space-x-1 items-center">
					@include('partials.permission-tag', $user)
					<h2 class="text-2xl font-bold">
						{{ $user->name }}
						<span class="text-gray-500 text-lg font-normal">{{ $user->username }}</span>
					</h2>
				</div>
			</div>
		</a>
		<div>
		@if ($panel)
			@include('partials.block-btn', $user)
			@if (Auth::user()->isAdmin())
			<button onclick="showEditUserModal({{ $user->id }})" class="tool-link">
				<i class="fa-solid fa-pencil"></i>
				<span class="max-md:hidden ml-1">Edit</span>
			</button>
			<button onclick="deleteUser({{ $user->id }})" class="tool-link text-red-500">
				<i class="fa-solid fa-trash"></i>
				<span class="max-md:hidden ml-1">Delete</span>
			</button>
			@endif
		</div>
		@endif
	</div>	
</article>