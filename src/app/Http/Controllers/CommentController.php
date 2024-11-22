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

		$post->text = $request->text;

		$post->save();
		$comment->save();

        return view('partials.comment', [
			'comment' => $comment
		]);
    }
}
