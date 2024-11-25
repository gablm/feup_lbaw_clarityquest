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
            'id' => 'required|integer|exists:posts,id', // Validate that id corresponds to an existing post
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

    /**
     * Create Notification
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:255',
            'commentable_id' => 'required|integer',
            'commentable_type' => 'required|string',
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'commentable_id' => $validated['commentable_id'],
            'commentable_type' => $validated['commentable_type'],
            'user_id' => auth()->id(),
        ]);

        // Determine the owner to notify
        $ownerId = null;
        if ($validated['commentable_type'] === 'App\Models\Question') {
            $ownerId = Question::find($validated['commentable_id'])->user_id;
        } elseif ($validated['commentable_type'] === 'App\Models\Answer') {
            $ownerId = Answer::find($validated['commentable_id'])->user_id;
        }

        if ($ownerId && $ownerId !== auth()->id()) {
            Notification::create([
                'receiver' => $ownerId,
                'description' => 'Someone commented on your ' . ($validated['commentable_type'] === 'App\Models\Question' ? 'question' : 'answer') . '.',
                'type' => 'MENTION',
            ]);
        }

        return redirect()->back()->with('success', 'Comment added.');
    }
}


