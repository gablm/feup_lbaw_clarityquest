@php
$profile_pic = $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');
@endphp
<div>
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
			@if ($panel && Auth::user()->id != $user->id)
			<div class="flex flex-row">
				@if ($user->isElevated() == false || ($user->isElevated() && Auth::user()->isAdmin()))
				<button onclick="blockUser({{ $user->id }})" class="tool-link">
					@if ($user->isBlocked())
					<i class="fa-solid fa-unlock"></i>
					<span class="max-md:hidden ml-1">Unblock</span>
					@else
					<i class="fa-solid fa-shield-halved"></i>
					<span class="max-md:hidden ml-1">Block</span>
					@endif
				</button>
				@endif
				@if (Auth::user()->isAdmin())
				<a href="{{ url('/users/' . $user->id . '/edit') }}" class="tool-link">
					<i class="fa-solid fa-pencil"></i>
					<span class="max-md:hidden ml-1">Edit</span>
				</a>
				<button onclick="showDeleteModal({{ $user->id }}, deleteUser, setupDeleteUser)" class="tool-link text-red-500">
					<i class="fa-solid fa-trash"></i>
					<span class="max-md:hidden ml-1">Delete</span>
				</button>
				@endif
			</div>
			@endif
		</div>
	</article>
	@if(isset($error))
		<div class="error-message text-red-500 mt-2">
			{{ $error }}
		</div>
	@endif
</div>