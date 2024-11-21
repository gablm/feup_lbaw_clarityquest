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
}
