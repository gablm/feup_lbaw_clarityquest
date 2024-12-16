@php
	$user = $report->user;
	$post = $report->post;
	$infr = $post->user;
	
	$url = $post->id;

	$answer = \App\Models\Answer::find($post->id);
	if ($answer != null)
		$url = $answer->question->id . "#" . $answer->id;
	
	$comment = \App\Models\Comment::find($post->id);
	if ($comment != null)
	{
		$owner = $comment->post_id;
		$a2 = \App\Models\Answer::find($owner);
		$url = $a2 != null ?
			$a2->question->id . "#" . $comment->id
			: $owner . "#" . $comment->id;
	}

	$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');
	$profile_pic2 = $infr && $infr->profile_pic ? asset($infr->profile_pic) : url('img/default_pic.png');
@endphp

<article class="report-card w-full bg-white shadow-md rounded-lg mb-4" data-id="{{ $report->id }}">
	<div class="flex flex-col space-y-2 p-4">
		<div class="flex flex-row justify-between">
			<div class="flex flex-row items-center text-gray-500">
				<span class="mr-2">Posted by: </span>
				<a class="tool-link" href="{{ $infr ? url('/users/' . $infr->id) : '/' }}">
					<div class="flex flex-row items-center">
						<img src="{{ $profile_pic2 }}" alt="Profile Picture" class="w-6 h-6 rounded-full object-cover">
						<span class="ml-2">{{ $infr->name ?? "[REDACTED]" }}</span>
					</div>
				</a>
			</div>
			<div class="flex flex-row items-center text-gray-500">
				<span class="mr-2">Reported by: </span>
				<a class="tool-link" href="{{ $user ? url('/users/' . $user->id) : '/' }}">
					<div class="flex flex-row items-center">
						<img src="{{ $profile_pic }}" alt="Profile Picture" class="w-6 h-6 rounded-full object-cover">
						<span class="ml-2">{{ $user->name ?? "[REDACTED]" }}</span>
					</div>
				</a>
			</div>
		</div>
		<span>{{ $report->reason }}</span>
		<span class="text-gray-500">{{ $post->text }}</span>
		<div class="flex space-x-2">
			<a href="{{ url('/questions/' . $url )}}" class="tool-link">
				<i class="fa-solid fa-eye"></i>
				<span class="max-md:hidden ml-1">View Context</span>
			</a>
			<button onclick="showDeleteModal({{ $report->id }}, deleteReport, setupDeleteReport)" class="tool-link text-red-500">
				<i class="fa-solid fa-trash"></i>
				<span class="max-md:hidden ml-1">Delete</span>
			</button>
		</div>
	</div>
</article>