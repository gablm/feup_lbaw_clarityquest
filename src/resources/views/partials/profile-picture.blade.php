@if ($user && $user->profile_pic)
<img 
	src="{{ asset($user->profile_pic) }}"
	alt="Profile Picture"
	class="h-{{ $size }} w-{{ $size }} rounded-full object-cover">
@else
<img 
	src="{{ url('img/default_pic.png') }}"
	alt="Default Profile Picture"
	class="h-{{ $size }} w-{{ $size }} rounded-full object-cover">
@endif