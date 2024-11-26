<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Edition;
use App\Models\Post;
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

		return view('partials.comment', [
			'comment' => $comment
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
    
                return Comment::create([
                    'id' => $post->id,  // Assuming 'id' here refers to the Post's ID
                    'post_id' => $ownerPost->id
                ]);
            });
    
            return view('partials.comment', [
                'comment' => $comment
            ]);
    
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred while creating the comment.']);
        }
    }
}


