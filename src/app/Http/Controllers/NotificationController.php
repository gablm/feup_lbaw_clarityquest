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
     * Delete a notification for the authenticated user.
     * 
     * @param int $id The ID of the notification to delete.
     */
    public function delete($id)
    {
        $notification = Notification::findOrFail($id);

		$this->authorize('delete', $notification);

        $notification->delete();
    
        return response()->json(['success' => true]);
    }  
}