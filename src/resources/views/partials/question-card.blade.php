@php
$post = $question->post;
$user = $post->user;

$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$owner = $user && Auth::check() && $user->id == Auth::user()->id;
$elevated = Auth::check() && Auth::user()->isElevated();
@endphp

<article class="w-full bg-white shadow-md space-x-6 rounded-lg mb-4" data-id="{{ $question->id }}">
	<a href={{ url('/questions/' . $question->id) }}>
		<div class="flex flex-col p-4">
			<div class="flex flex-row justify-between space-x-6 items-center text-gray-500 text-sm mb-1">
				<div class="flex flex-row items-center">
					<img
						src="{{ $profile_pic }}"
						alt="Profile Picture"
						class="w-5 h-5 rounded-full object-cover">
					<span class="ml-1">{{ $question->post->user->name ?? "[REDACTED]" }}</span>
				</div>
				<span>{{ $post->creationFTime() }}</span>
			</div>
			<h5 class="text-lg font-semibold break-words">{{ $question->title }}</h5>
			<p class="text-gray-700 mb-3 mt-1 break-words">{{ \Illuminate\Support\Str::limit($post->text, 150, '...') }}</p>
			<div class="flex flex-row justify-between space-x-6 text-gray-500 text-sm">
				<div>
					<i class="fa-solid fa-up-down"></i>
					<span>{{ $question->post->votes }}</span>
				</div>
				<div class="flex space-x-2">
					<span> {{ $question->answers->count() }} Answer(s)</span>
					<span>{{ $post->comments->count() }} Comment(s)</span>
				</div>
			</div>
		</div>
	</a>
</article>