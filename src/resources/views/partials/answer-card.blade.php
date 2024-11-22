@php
$post = $answer->post;
$user = $post->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$owner = $user && Auth::check() && $user->id == Auth::user()->id;
$elevated = Auth::check() && Auth::user()->isElevated();
@endphp

<article class="w-full bg-white shadow-md rounded-lg mb-4" data-id="{{ $answer->id }}">
	<a href={{ url('/questions/' . $answer->question->id) }}>
		<div class="flex flex-col p-4">
			<div class="flex flex-row justify-between space-x-6 items-center text-gray-500 text-sm mb-1">
				<div class="flex flex-row items-center">
					<img
						src="{{ $profile_pic }}"
						alt="Profile Picture"
						class="w-5 h-5 rounded-full object-cover">
					<span class="ml-1">{{ $post->user->name ?? "[REDACTED]" }}</span>
				</div>
				<span class="text-gray-500 text-sm">On question: {{ $answer->question->title }}</span>
				<span>{{ $post->creationFTime() }}</span>
			</div>
			<p class="text-gray-700 mb-3 mt-1">{{ $post->text }}</p>
			
			<div class="flex flex-row justify-between space-x-6 text-gray-500 text-sm">
				<div>
					<i class="fa-solid fa-up-down"></i>
					<span>{{ $post->votes }}</span>
				</div>
				<div class="flex space-x-2">
					<span>{{ $post->comments->count() }} Comment(s)</span>
				</div>
			</div>
		</div>
	</a>
</article>