<nav class="bg-gray-400 py-1 px-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="flex items-center">
            <img src="{{ url('img/logo.png') }}" alt="Logo" class="h-20 w-auto">
        </a>
        <div class="flex items-center flex-grow space-x-4 ml-4">
            <form class="flex items-center flex-grow space-x-2">
                <input type="search" placeholder="Search" aria-label="Search" class="flex-grow px-2 py-1 rounded-md">
                <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600">Search</button>
            </form>
            <ul class="flex space-x-4">
                <li>
                    <a href="{{ url('/') }}" class="text-white hover:text-gray-300">Home</a>
                </li>
                <li class="relative">
                    <a href="#" id="inboxDropdown" class="text-white hover:text-gray-300">Inbox</a>
                </li>
                <li>
                    <a href="/profile" class="text-white hover:text-gray-300">Profile</a>
                </li>
            </ul>
        </div>
    </div>
</nav>