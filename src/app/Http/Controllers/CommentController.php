<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\Edition;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Exception;

class CommentController extends Controller
{
	/**
	 * Display a comment.
	 */
	public function show(string $id)
	{
		$comment = Comment::findOrFail($id);

		$this->authorize('show', $comment);

		$user = Auth::user();
		$voteStatus = null;
	
		if ($user) {
			$vote = $question->post->votes()->where('user_id', $user->id)->first();
			$voteStatus = $vote ? ($vote->positive ? 'positive' : 'negative') : null;
		}
	
		return view('partials.comment', [
			'comment' => $comment,
			'voteStatus' => $voteStatus
		]);
	}

	/**
	 * Delete a comment.
	 */
	public function delete(string $id)
	{
		$comment = Comment::findOrFail($id);

		$this->authorize('delete', $comment);

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

		DB::transaction(function () use ($comment) {
			$comment->post->delete();
			$comment->delete();
		});
		return;
	}

	/**
	 * Update a comment.
	 */
	public function update(Request $request, string $id)
	{
		$comment = Comment::findOrFail($id);
		$post = $comment->post;

		$this->authorize('update', $comment);

		$request->validate([
			'text' => 'required|string|max:10000'
		]);

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

		DB::transaction(function () use ($request, $post, $comment) {
			$old_text = $post->text;
			$post->text = $request->text;

			$post->save();
			$comment->save();

			Edition::create([
				'post_id' => $comment->id,
				'old_title' => null,
				'new_title' => null,
				'old' => $old_text,
				'new' => $request->text,
			]);
		});

		return view('partials.comment', [
			'comment' => $comment
		]);
<<<<<<< HEAD
    }
    /**
     * Create a new comment.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
    
        $request->validate([
            'text' => 'required|string|max:1000',
            'id' => 'required|integer|exists:posts,id', 
        ]);
    
        $ownerPost = Post::findOrFail($request->id);
    
        try {
            $comment = DB::transaction(function () use ($request, $user, $ownerPost) {
                $post = Post::create([
                    'text' => $request->text,
                    'user_id' => $user->id
                ]);
                
                

                $comment = Comment::create([
                    'id' => $post->id, 
                    'post_id' => $ownerPost->id
                ]);
                

                return $comment;
            });
    
            return view('partials.comment', [
                'comment' => $comment
            ]);
    
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred while creating the comment.']);
        }
    }

}

=======
	}
	/**
	 * Create a new comment.
	 */
	public function create(Request $request)
	{
		$user = Auth::user();

		$request->validate([
			'text' => 'required|string|max:1000',
			'id' => 'required|integer|exists:posts,id',
		]);

		$ownerPost = Post::findOrFail($request->id);

		$comment = DB::transaction(function () use ($request, $user, $ownerPost) {
			$post = Post::create([
				'text' => $request->text,
				'user_id' => $user->id
			]);

			$question = Question::find($ownerPost->id);
			$answer = Answer::find($ownerPost->id);

			$content = $question ? "question titled '{$question->title}'" : "answer to '{$answer->question->title}'";

			$notification = Notification::create([
				'receiver' => $post->user_id,
				'description' => "Your {$content} received a comment by '{$user->username}'.",
				'type' => 'RESPONSE',
			]);

			DB::table('notificationpost')->insert([
				'notification_id' => $notification->id,
				'post_id' => $question ? $question->id : $answer->question->id
			]);

			if ($question) {
				foreach ($question->follows as $follower)
				{
					$notification = Notification::create([
						'receiver' => $follower->id,
						'description' => "The {$content} you follow just received a comment by '{$user->username}'.",
						'type' => 'RESPONSE',
					]);

					DB::table('notificationpost')->insert([
						'notification_id' => $notification->id,
						'post_id' => $question ? $question->id : $answer->question->id
					]);
				}
			}

			return Comment::create([
				'id' => $post->id,
				'post_id' => $ownerPost->id
			]);
		});

		return view('partials.comment', [
			'comment' => $comment
		]);
	}
}
>>>>>>> 69db8eab063a08ba41bc5c38ec447a326900a579
