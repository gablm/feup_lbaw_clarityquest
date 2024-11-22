<nav class="fixed w-screen bg-white z-0 shadow-lg px-4">
    <div class="flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ url('img/logo.png') }}" alt="Logo" class="h-20 w-auto">
        </a>
        <form class="flex items-center w-[30vw] space-x-1" action="{{ route('search') }}"  method="GET">
            <input type="search" name='search' placeholder="Search" aria-label="Search" class="nav" value="{{ request('search') }}" >
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
                <a href="#" id="inboxDropdown" class="nav">
                    <button class="nav-secondary">
                        <i class="fa-solid fa-envelope"></i>
                        <span class="max-sm:hidden">Inbox</span>
                    </button>
                </a>
            </li>
            <li>
                <a href="{{ url('/profile') }}" class="nav">
					<img
					@if (Auth::user()->profile_pic)
						src="{{ asset(Auth::user()->profile_pic) }}"
					@else
						src="{{ url('img/default_pic.png') }}"
					@endif
						alt="Profile Picture"
						class="w-10 h-10 rounded-full object-cover">
                </a>
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