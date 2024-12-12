<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Vote;
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
        if (!$user) {
            return redirect()->route('login');
        }
        $positive = $request->positive === "true";

        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        DB::transaction(function () use ($user, $id, $positive) {
            $post = Post::findOrFail($id);

            $existingVote = DB::table('votes')
                ->where('user_id', $user->id)
                ->where('post_id', $post->id)
                ->first();

            if ($existingVote) {
                if ($existingVote->positive == $positive) {

                    DB::table('votes')
                        ->where('user_id', $user->id)
                        ->where('post_id', $post->id)
                        ->delete();
                } else {

                    DB::table('votes')
                        ->where('user_id', $user->id)
                        ->where('post_id', $post->id)
                        ->update(['positive' => $positive]);
                }
            } else {

                DB::table('votes')->insert([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'positive' => $positive,
                ]);
            }
        });

        $post = Post::findOrFail($id);

        return view('partials.vote',
            ['id' => $post->id, 
            'votes' => $post->votes, 
            'voteStatus' => $post->voteStatus($user->id)]);
    }
}