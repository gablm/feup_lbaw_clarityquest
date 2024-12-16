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
        $request->validate([
            'positive' => 'required|string',
        ]);

        $user = Auth::user();
        if ($user == null)
            return redirect()->route('login');

        $positive = $request->positive === "true";

        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        DB::transaction(function () use ($user, $id, $positive) {
            $post = Post::findOrFail($id);

            $existingVote = DB::table('votes')
                ->where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->first();

            if ($existingVote == null) {
				DB::table('votes')->insert([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'positive' => $positive,
                ]);

                return;
            }

			if ($existingVote->positive == $positive) {
				DB::table('votes')
					->where('user_id', $user->id)
					->where('post_id', $post->id)
					->delete();
				return;
			}

			DB::table('votes')
				->where('user_id', $user->id)
				->where('post_id', $post->id)
				->update(['positive' => $positive]);
        });

        $post = Post::findOrFail($id);

        $voteCount = DB::table('votes')
			->where('post_id', $post->id)->count();

        if ($post->user_id != null && ($voteCount <= 10 || $voteCount % 10 === 0))
		{
            $message = "Your post has reached {$voteCount} vote(s)!";
            $notification = Notification::create([
                'receiver' => $post->user_id,
                'description' => $message,
            ]);
    
            DB::table('notificationpost')->insert([
                'notification_id' => $notification->id,
                'post_id' => $post->id
            ]);
        }

        return view('partials.vote',
            ['id' => $post->id, 
            'votes' => $post->votes, 
            'voteStatus' => $post->voteStatus($user->id)]);
    }

}