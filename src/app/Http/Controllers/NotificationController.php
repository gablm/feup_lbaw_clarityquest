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
<<<<<<< HEAD
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
=======
>>>>>>> 69db8eab063a08ba41bc5c38ec447a326900a579
     * Delete a notification for the authenticated user.
     * 
     * @param int $id The ID of the notification to delete.
     */
    public function delete($id)
    {
<<<<<<< HEAD
        
        $notification = Notification::findOrFail($id);

        
    
        $notification->delete();
    
        return response()->json(['success' => true]);
    }
        
=======
        $notification = Notification::findOrFail($id);

		$this->authorize('delete', $notification);

        $notification->delete();
    
        return response()->json(['success' => true]);
    }  
>>>>>>> 69db8eab063a08ba41bc5c38ec447a326900a579
}