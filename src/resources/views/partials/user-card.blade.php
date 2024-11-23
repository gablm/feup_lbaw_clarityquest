@php
$profile_pic = $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');
@endphp

<article class="w-full bg-white shadow-md space-x-4 rounded-lg" data-id="{{ $user->id }}">
	<a href={{ url('/users/' . $user->id) }}>
		<div class="p-4">
			<div class="flex flex-row space-x-3 items-center text-gray-500 text-sm mb-1">
				<div class="flex flex-row items-center">
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
		</div>
	</a>
</article>