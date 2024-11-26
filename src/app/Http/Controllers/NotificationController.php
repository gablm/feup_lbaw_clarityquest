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
        $notifications = Notification::where('receiver', Auth::id())
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
        $notifications = Notification::where('receiver', Auth::id())
            ->orderBy('sent_at', 'desc')
            ->get();

        return view('pages.notifications', compact('notifications'));
    }

    /**
     * Create a new notification for a specific user.
     * 
     * @param int $receiverId The ID of the user receiving the notification.
     * @param string $description The notification description.
     * @param string $type The notification type.
     */
    public function create($receiverId, $description, $type = 'OTHER')
    {
        Notification::create([
            'receiver' => $receiverId,
            'description' => $description,
            'type' => $type,
        ]);

        return response()->json(['message' => 'Notification created successfully.']);
    }

    /**
     * Delete a notification for the authenticated user.
     * 
     * @param int $id The ID of the notification to delete.
     */
    public function delete($id)
    {
        try {
            $notification = Notification::findOrFail($id);

            if ($notification->receiver !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }
    
            $notification->delete();
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }
}