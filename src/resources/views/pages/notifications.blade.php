@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Notifications</h2>
        
        <!-- Notifications List -->
        <ul class="list-group">
            @forelse ($notifications as $notification)
                <li class="list-group-item {{ $notification->read ? '' : 'list-group-item-warning' }}">
                    {{ $notification->description }}
                    <a href="{{ route('notifications.read', $notification->id) }}" class="btn btn-sm btn-primary float-end">Mark as Read</a>
                </li>
            @empty
                <li class="list-group-item">No notifications found</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection