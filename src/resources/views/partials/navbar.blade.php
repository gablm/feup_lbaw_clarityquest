<nav class="bg-gray-800 p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="{{ url('/') }}" class="text-white text-lg font-semibold">Logo</a>
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
                    <ul class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-md hidden group-hover:block">
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Notifications</a></li>
                        <li><a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Messages</a></li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="text-white hover:text-gray-300">Profile</a>
                </li>
            </ul>
        </div>
    </div>
</nav>