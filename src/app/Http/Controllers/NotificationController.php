<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Fetch the most recent notifications for the authenticated user.
     */
    public function recent()
    {
        $notifications = Notification::where('receiver', auth()->id())
            ->orderBy('sent_at', 'desc')
            ->take(4)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Fetch all notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = Notification::where('receiver', auth()->id())
            ->orderBy('sent_at', 'desc')
            ->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('receiver', auth()->id())
            ->firstOrFail();

        $notification->update(['read' => true]);

        return redirect()->back();
    }
}
