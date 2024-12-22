<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        // Validate the request to ensure 'reason' is present and is a string, and 'id' is a valid post ID
        $request->validate([
            'reason' => 'required|string|max:100',
            'id' => 'required|integer|exists:posts,id'
        ]);

        // Check if the user is blocked
        if (Auth::user()->isBlocked())
            return abort(403);

        // Find the post by ID or fail if not found
        $post = Post::findOrFail($request->id);

        // Create a new report with the provided reason, user ID, and post ID
        Report::create([
            'reason' => $request->reason,
            'user_id' => Auth::user()->id,
            'post_id' => $post->id
        ]);

        // Return a JSON response indicating success
        return response()->json(['success' => true]);
    }

    public function delete(Request $request, string $id)
    {
        // Find the report by ID or fail if not found
        $report = Report::findOrFail($id);

        // Authorize the user to delete the report
        $this->authorize('delete', $report);

        // Delete the report
        $report->delete();
    }
}