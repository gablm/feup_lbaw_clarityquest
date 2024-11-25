@extends('layouts.app')

@section('content')
<div class="flex flex-row flex-grow">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-semibold mb-4">Notifications</h2>
        
        @forelse ($notifications as $notification)
            @include('partials.notification-card', ['notification' => $notification])
        @empty
            <div class="text-center text-gray-500">
                <p>No notifications found.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection