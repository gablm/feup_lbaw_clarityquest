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
		$positive = null;
		switch ($request->positive)
		{
			case "1":
				$positive = true;
				break;
			case "0":
				$positive = null;
				break;
			case "-1":
				$positive = false;
				break;
		}

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
		DB::transaction(function () use ($user, $id, $positive) {
			$post = Post::findOrFail($id);

			if ($positive == null)
			{
				DB::table('votes')
					->where('user_id', $user->id)
					->where('post_id', $post->id)->delete();
				return;
			}

			DB::table('votes')->updateOrInsert(
				['user_id' => $user->id, 'post_id' => $post->id],
				['positive' => $positive]
			);
		});

		$post = Post::findOrFail($id);

		return response()->json(['votes' => $post->votes]);
	}
}
