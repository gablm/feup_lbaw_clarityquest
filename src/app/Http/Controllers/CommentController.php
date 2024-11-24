<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a comment.
     */
    public function show(string $id)
    {
        $comment = Comment::findOrFail($id);

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

		$comment->post->delete();
		$comment->delete();

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
<<<<<<< Updated upstream

		$post->text = $request->text;
=======
        
        $old_text = $post->text;
        $post->text = $request->text;
>>>>>>> Stashed changes

		$post->save();
		$comment->save();

        Edition::create([
            'post_id' => $comment->id,
            'old_title' => null, 
            'new_title' => null, 
            'old' => $old_text, 
            'new' => $request->text,
        ]);

        return view('partials.comment', [
			'comment' => $comment
		]);
    }
<<<<<<< Updated upstream
}
=======

    /**
     * Create a new comment.
     */
    public function create(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:1000',
            'postId' => 'required|integer',
        ]);

        $user = Auth::user();

        // Check if the postId exists in either the Question or Answer table
        $isQuestion = Question::where('id', $request->postId)->exists();
        $isAnswer = Answer::where('id', $request->postId)->exists();

        if (!$isQuestion && !$isAnswer) {
            return response()->json(['error' => 'Invalid post ID'], 400);
        }

        $comment = new Comment();
        $comment->text = $request->text;
        $comment->post_id = $request->postId;
        $comment->user_id = $user->id;
        $comment->save();


        return view('partials.comment', [
            'comment' => $comment
        ]);
    }
}
>>>>>>> Stashed changes
