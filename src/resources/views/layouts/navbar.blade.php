<nav class="fixed w-screen bg-white z-0 shadow-lg px-4">
    <div class="flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ url('img/logo.png') }}" alt="Logo" class="h-20 w-auto">
        </a>
        <form class="flex items-center w-[30vw] space-x-1">
            <input type="search" placeholder="Search" aria-label="Search" class="nav">
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
            <li>
                <a href="{{ url('/about') }}">
                    <button class="nav-secondary">About</button>
                </a>
            </li>
            @if (Auth::check())
            <li class="relative">
                <a href="#" id="inboxDropdown" class="nav space-x-2">
                    <button class="nav-secondary">
                        <i class="fa-solid fa-envelope"></i>
                        Inbox
                    </button>
                </a>
            </li>
            <li>
                <a href="{{ url('/profile') }}" class="nav">
                    <button class="nav-secondary">Profile</button>    
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