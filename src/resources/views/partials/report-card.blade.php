@php
$user = $report->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');
@endphp

<article class="w-full bg-white shadow-md rounded-lg mb-4" data-id="{{ $report->id }}">
	<div class="flex flex-col space-y-2 p-4">
		<div class="flex flex-row items-center text-gray-500">
			<span class="mr-2">Reported by: </span>
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
		<span>{{ $report->reason }}</span>
		<span class="text-gray-500">{{ $report->post->text }}</span>
	</div>
</article>