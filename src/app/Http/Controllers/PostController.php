<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Vote;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
	/**
	 * Vote on a question.
	 */   
    public function vote(Request $request, $id)
    {
        // Validate the request to ensure 'positive' is present and is a string
        $request->validate([
            'positive' => 'required|string',
        ]);

        // Get the authenticated user
        $user = Auth::user();
        // If the user is not authenticated, redirect to the login page
        if ($user == null)
            return redirect()->route('login');

        // Determine if the vote is positive or negative
        $positive = $request->positive === "true";

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        // Start a database transaction
        DB::transaction(function () use ($user, $id, $positive) {
            // Find the post by ID or fail if not found
            $post = Post::findOrFail($id);

            // Check if the user has already voted on this post
            $existingVote = DB::table('votes')
                ->where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->first();

            // If no existing vote, insert a new vote
            if ($existingVote == null) {
                DB::table('votes')->insert([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'positive' => $positive,
                ]);
                return;
            }

            // If the existing vote is the same as the new vote, delete the vote (toggle off)
            if ($existingVote->positive == $positive) {
                DB::table('votes')
                    ->where('user_id', $user->id)
                    ->where('post_id', $post->id)
                    ->delete();
                return;
            }

            // Otherwise, update the existing vote to the new value
            DB::table('votes')
                ->where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->update(['positive' => $positive]);
        });

        // Find the post again to get the updated vote count
        $post = Post::findOrFail($id);

        // Count the total number of votes for the post
        $voteCount = DB::table('votes')
            ->where('post_id', $post->id)->count();

        // If the post has a user and the vote count is a milestone (<= 10 or multiple of 10)
        if ($post->user_id != null && ($voteCount <= 10 || $voteCount % 10 === 0)) {
            // Create a notification message
            $message = "Your post has reached {$voteCount} vote(s)!";
            // Create a new notification
            $notification = Notification::create([
                'receiver' => $post->user_id,
                'description' => $message,
            ]);

            // Link the notification to the post
            DB::table('notificationpost')->insert([
                'notification_id' => $notification->id,
                'post_id' => $post->id
            ]);
        }

        // Return the updated vote partial view with the post's vote data
        return view('partials.vote', [
            'id' => $post->id, 
            'votes' => $post->votes, 
            'voteStatus' => $post->voteStatus($user->id)
        ]);
    }
}