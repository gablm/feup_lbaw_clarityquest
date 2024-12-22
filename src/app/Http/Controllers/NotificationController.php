<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Fetch the most recent notifications for the authenticated user.
     */
    public function recent()
    {
        // Get the most recent 4 notifications for the authenticated user, ordered by sent date
        $notifications = Notification::where('receiver', Auth::id())
            ->orderBy('sent_at', 'desc')
            ->take(4)
            ->get();

        // Return the notifications as a JSON response
        return response()->json($notifications);
    }

    /**
     * Fetch all notifications for the authenticated user.
     */
    public function index()
    {
        // Check if the user is blocked
        if (Auth::user()->isBlocked())
            return abort(403);

        // Get all notifications for the authenticated user, ordered by sent date
        $notifications = Notification::where('receiver', Auth::id())
            ->orderBy('sent_at', 'desc')
            ->get();

        // Return the view with the notifications
        return view('pages.notifications', compact('notifications'));
    }

    /**
     * Delete a notification for the authenticated user.
     * 
     * @param int $id The ID of the notification to delete.
     */
    public function delete($id)
    {
        // Find the notification by ID or fail if not found
        $notification = Notification::findOrFail($id);

        // Authorize the user to delete the notification
        $this->authorize('delete', $notification);

        // Delete the notification
        $notification->delete();
    
        // Return a JSON response indicating success
        return response()->json(['success' => true]);
    }  
}