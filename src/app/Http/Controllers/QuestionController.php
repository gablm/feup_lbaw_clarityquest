<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Question;
use App\Models\Edition;
use App\Models\Notification;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    /**
     * Display a listing of the followed questions.
     *
     * @return \Illuminate\View\View
     */
    public function followedQuestions()
    {
        // Check if the user is blocked
        if (Auth::user()->isBlocked())
            return abort(403);

        // Get the authenticated user
        $user = Auth::user();
        // Get the questions followed by the user
        $followedQuestions = $user->followedQuestions;

        // Return the view with the followed questions
        return view('questions.followed', compact('followedQuestions'));
    }

    public function myQuestions()
    {
        // Check if the user is blocked
        if (Auth::user()->isBlocked())
            return abort(403);

        // Get the authenticated user
        $user = Auth::user();
        // Get the questions created by the user
        $myQuestions = $user->questionsCreated;

        // Return the view with the user's questions
        return view('questions.mine', compact('myQuestions'));
    }

    /**
     * Creates a new question.
     */
    public function create(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Check if the user is blocked
        if ($user->isBlocked())
            return abort(403);

        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:250',
            'description' => 'required|string|max:3000',
            'tags' => 'required'
        ]);

        // Create the question within a database transaction
        $question = DB::transaction(function () use ($request, $user) {
            // Create a new post
            $post = Post::create([
                'text' => $request->description,
                'user_id' => $user->id
            ]);
            // Create a new question
            $question = Question::create([
                'title' => $request->title,
                'id' => $post->id
            ]);

            // Attach tags to the question
            foreach ($request->tags as $tag_id) {
                $tag = Tag::find($tag_id);
                if ($tag == null) continue;

                DB::table('posttag')->insert([
                    'post_id' => $post->id,
                    'tag_id' => $tag->id
                ]);

                // Notify followers of the tag
                foreach ($tag->follows as $follower) {
                    $notification = Notification::create([
                        'receiver' => $follower->id,
                        'description' => "A new question titled '{$question->title}' by '{$user->username}' was asked with the tag '{$tag->name}'.",
                        'type' => 'RESPONSE',
                    ]);

                    DB::table('notificationpost')->insert([
                        'notification_id' => $notification->id,
                        'post_id' => $question->id
                    ]);
                }
            }

            return $question;
        });

        // Redirect to the newly created question with a success message
        return redirect("/questions/{$question->id}")
            ->withSuccess('Question created!');
    }

    /**
     * Display a create form.
     */
    public function showCreateForm()
    {
        // Check if the user is authenticated
        if (!Auth::check())
            return redirect('/');

        // Check if the user is blocked
        if (Auth::user()->isBlocked())
            return abort(403);

        // Get all tags
        $tags = Tag::all();

        // Return the view with the tags
        return view('questions.create', ['tags' => $tags]);
    }

    /**
     * Display a question.
     */
    public function show(string $id)
    {
        // Check if the user is authenticated and blocked
        if (Auth::check() && Auth::user()->isBlocked())
            return abort(403);

        // Find the question with its answers, ordered by correctness and votes
        $question = Question::with(['answers' => function ($query) {
            $query->join('posts', 'posts.id', '=', 'answers.id')
                  ->orderBy('answers.correct', 'desc')
                  ->orderBy('posts.votes', 'desc')
                  ->select('answers.*');
        }])->findOrFail($id);
    
        // Get all tags
        $tags = Tag::orderBy('name')->get();
    
        // Get the authenticated user
        $user = Auth::user();
        $voteStatus = null;
    
        // Determine the vote status of the user on the question
        if ($user) {
            $vote = $question->post->votes()->where('user_id', $user->id)->first();
            $voteStatus = $vote ? ($vote->positive ? 'positive' : 'negative') : null;
        }
    
        // Return the view with the question, tags, and vote status
        return view('questions.show', [
            'question' => $question,
            'tags' => $tags,
            'voteStatus' => $voteStatus
        ]);
    }

    /**
     * Delete a question.
     */
    public function delete(string $id)
    {
        // Find the question by ID or fail if not found
        $question = Question::findOrFail($id);
        // Authorize the user to delete the question
        $this->authorize('delete', $question);

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        // Start a database transaction
        DB::transaction(function () use ($question) {
            // Delete the post and the question
            $question->post->delete();
            $question->delete();
        });

        // Redirect to the home page with a success message
        return redirect('/')->withSuccess('Question deleted!');
    }

    /**
     * Update a question.
     */
    public function update(Request $request, string $id)
    {
        // Find the question by ID or fail if not found
        $question = Question::findOrFail($id);
        // Get the associated post
        $post = $question->post;
        // Get all tags
        $tags = Tag::orderBy('name')->get();

        // Authorize the user to update the question
        $this->authorize('update', $question);

        // Validate the request data
        $request->validate([
            'title' => 'required|string|max:64',
            'description' => 'required|string|max:10000'
        ]);

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

        // Start a database transaction
        DB::transaction(function () use ($request, $post, $question) {
            // Store the old title and text
            $old_title = $question->title;
            $question->title = $request->title;
            $old_text = $post->text;
            $post->text = $request->description;

            // Save the updated question and post
            $question->save();
            $post->save();

            // Create a new edition record
            Edition::create([
                'post_id' => $question->id,
                'old_title' => $old_title,
                'new_title' => $question->title,
                'old' => $old_text,
                'new' => $request->description,
            ]);
        });

        // Return the updated question partial view
        return view('partials.question', [
            'question' => $question,
            'tags' => $tags,
        ]);
    }

    public function follow(string $id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Find the question by ID or fail if not found
        $question = Question::findOrFail($id);
        // Authorize the user to view the question
        $this->authorize('show', $question);

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        // Start a database transaction
        DB::transaction(function () use ($user, $question) {
            // Check if the user already follows the question
            $exists = DB::table('followquestion')
                ->where('user_id', $user->id)
                ->where('question_id', $question->id)
                ->exists();

            // If the user already follows the question, unfollow it
            if ($exists) {
                DB::table('followquestion')
                    ->where('user_id', $user->id)
                    ->where('question_id', $question->id)
                    ->delete();
                return;
            }

            // Otherwise, follow the question
            DB::table('followquestion')->insert([
                'user_id' => $user->id,
                'question_id' => $question->id
            ]);
        });

        // Return the updated follow button partial view
        return view('partials.follow-btn', [
            'question' => $question
        ]);
    }

    /**
     * Add a tag to a question.
     */
    public function addTag(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'tag' => 'required|string',
        ]);

        // Find the question by ID or fail if not found
        $question = Question::findOrFail($id);
        // Authorize the user to modify the tags of the question
        $this->authorize('tags', $question);

        // Get the tag name from the request
        $tagName = trim($request->tag);
        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        // Start a database transaction
        DB::transaction(function () use ($question, $tagName) {
            // Find the tag by name or fail if not found
            $tag = Tag::where('name', $tagName)->firstOrFail();
            
            // Attach the tag to the question if not already attached
            if (!$question->tags->contains($tag->id))
                $question->tags()->attach($tag->id);
        });

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Tag added successfully.');
    }

    /**
     * Remove a tag from a question.
     */
    public function removeTag(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'tag' => 'required|string',
        ]);

        // Find the question by ID or fail if not found
        $question = Question::findOrFail($id);
        // Authorize the user to modify the tags of the question
        $this->authorize('tags', $question);

        // Get the tag name from the request
        $tagName = trim($request->tag);
        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        // Start a database transaction
        DB::transaction(function () use ($question, $tagName) {
            // Find the tag by name
            $tag = Tag::where('name', $tagName)->first();
            // Detach the tag from the question if it exists
            if ($tag) {
                $question->tags()->detach($tag->id);
            }
        });

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Tag removed successfully.');
    }
}