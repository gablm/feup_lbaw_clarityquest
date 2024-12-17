<nav aria-label="breadcrumb" class="mb-4">
    <ol class="list-reset flex text-sm text-gray-600">
        @foreach ($crumbs as $key => $crumb)
            @if ($key === count($crumbs) - 1)
                <li class="breadcrumb-item active text-gray-500" aria-current="page">
                    {{ $crumb['name'] }}
                </li>
            @else
                <li class="breadcrumb-item flex items-center">
                    <a href="{{ $crumb['url'] }}" class="text-blue-600 hover:underline">{{ $crumb['name'] }}</a>
                    <span class="mx-2">></span>
                </li>
            @endif
        @endforeach
    </ol>
</nav>