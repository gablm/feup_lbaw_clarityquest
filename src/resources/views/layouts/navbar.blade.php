@php

$user = Auth::user();
$profile_pic = $user && $user->profile_pic ? asset($user->profile_pic) : url('img/default_pic.png');

$notificationCount = $user ? $user->notifications()->count() : 0;

$notifications = $user ? $user->notifications()->orderBy('sent_at', 'desc')->take(4)->get() : collect();

@endphp

<nav class="fixed w-screen bg-white z-10 shadow-lg px-4">
	<div class="flex justify-between items-center px-4">
		<a href="{{ url('/') }}" class="flex items-center">
			<img alt="Clarity Quest Logo" src="{{ url('img/logo.png') }}" alt="Logo" class="h-20 w-auto">
		</a>
		<form class="flex items-center w-[30vw] space-x-1" action="{{ route('search') }}" method="GET">
			<input type="search" name='search' placeholder="Search" aria-label="Search" class="nav" value="{{ request('search') }}">
			<button type="submit" class="nav-search">
				<i class="fa-solid fa-magnifying-glass"></i>
			</button>
		</form>
		<ul class="flex items-center space-x-3">
			<li>
				<a href="{{ url('/') }}">
					<button class="nav-secondary">Home</button>
				</a>
			</li>
			@if (Auth::check())
			<li class="relative">
				<div class="relative inline-block text-left">
					<div>
						<button class="nav-secondary" onclick="toggleNotificationDropdown()">
						<i class="fa-solid fa-bell"></i>
							@if ($notificationCount > 0)
							<span class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
								{{ $notificationCount }}
							</span>
							@endif
						</button>
					</div>
					<div id="notification-dropdown" class="hidden absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
					<a class="block px-4 pb-2 pt-3 text-md font-bold text-gray-900">
						Notifications
					</a>
						@if ($notifications->isEmpty())
							<p class="px-4 py-2 text-sm text-gray-500">No notifications</p>
						@else
							<ul>
								@foreach ($notifications as $notification)
								@php
								$post = $notification->posts()->first();
								$route = $post ? url('/questions/' . $post->id ) : route('pages.notifications');
								@endphp
								<li class="px-4 py-2 border-b hover:bg-gray-100">
									<a href="{{ $route }}" class="block text-sm text-gray-700">
										{{ $notification->description }}
									</a>
								</li>
								@endforeach
							</ul>
							<div class="p-2 text-center">
								<a href="{{ route('pages.notifications') }}" class="text-blue-500 text-sm">View all notifications</a>
							</div>
						@endif
					</div>
				</div>
			</li>
			<li>
				<div class="relative inline-block text-left">
					<div>
						<button class="nav" onclick="toggleUserDropdown()">
							<img
								src="{{ $profile_pic }}"
								alt="Profile Picture"
								class="ml-1 w-10 h-10 rounded-full object-cover">
						</button>
					</div>
					<div id="user-dropdown" class="hidden absolute right-0 z-10 w-56 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 focus:outline-none">
						<div>
							<a href="{{ route('profile') }}" class="block px-4 pb-2 pt-3 text-sm text-gray-700 hover:bg-gray-200 hover:rounded-t-md">Profile</a>
							@if ($user->isElevated())
							<a href="{{ route('admin') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-200">Admin Panel</a>
							@endif
						</div>
						<form method="POST" action="{{ route('logout') }}">
							@csrf
							<a href="#" class="block px-4 pt-2 pb-3 text-sm text-red-500 hover:bg-gray-200 hover:rounded-b-md" onclick="this.parentElement.submit()">
								Log Out
							</a>
						</form>
					</div>
				</div>
			</li>
			@else
			<li>
				<a href="{{ url('/login') }}">
					<button class="nav-main">Login</button>
				</a>
			</li>
			@endif
		</ul>
	</div>
</nav>
