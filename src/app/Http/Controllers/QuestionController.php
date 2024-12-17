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
		if (Auth::user()->isBlocked())
			return abort(403);

		$user = Auth::user();
		$followedQuestions = $user->followedQuestions;

		return view('questions.followed', compact('followedQuestions'));
	}

	public function myQuestions()
	{
		if (Auth::user()->isBlocked())
			return abort(403);

		$user = Auth::user();
		$myQuestions = $user->questionsCreated;

		return view('questions.mine', compact('myQuestions'));
	}

	/**
	 * Creates a new question.
	 */
	public function create(Request $request)
	{
		$user = Auth::user();

		if ($user->isBlocked())
			return abort(403);

		$request->validate([
			'title' => 'required|string|max:250',
			'description' => 'required|string|max:3000',
			'tags' => 'required'
		]);

		$question = DB::transaction(function () use ($request, $user) {
			$post = Post::create([
				'text' => $request->description,
				'user_id' => $user->id
			]);
			$question = Question::create([
				'title' => $request->title,
				'id' => $post->id
			]);

			foreach ($request->tags as $tag_id) {
				$tag = Tag::find($tag_id);
				if ($tag == null) continue;

				DB::table('posttag')->insert([
					'post_id' => $post->id,
					'tag_id' => $tag->id
				]);

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

		return redirect("/questions/{$question->id}")
			->withSuccess('Question created!');
	}
	/**
	 * Display a create form.
	 */
	public function showCreateForm()
	{
		if (!Auth::check())
			return redirect('/');

		if (Auth::user()->isBlocked())
			return abort(403);

		$tags = Tag::all();

		return view('questions.create', ['tags' => $tags]);
	}

	/**
	 * Display a question.
	 */
	public function show(string $id)
	{
		if (Auth::check() && Auth::user()->isBlocked())
			return abort(403);

		$question = Question::with(['answers' => function ($query) {
			$query->join('posts', 'posts.id', '=', 'answers.id')
				  ->orderBy('answers.correct', 'desc')
				  ->orderBy('posts.votes', 'desc')
				  ->select('answers.*');
		}])->findOrFail($id);
	
		$tags = Tag::orderBy('name')->get();
	
		$user = Auth::user();
		$voteStatus = null;
	
		if ($user) {
			$vote = $question->post->votes()->where('user_id', $user->id)->first();
			$voteStatus = $vote ? ($vote->positive ? 'positive' : 'negative') : null;
		}
	
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
		$question = Question::findOrFail($id);
		$this->authorize('delete', $question);

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
		DB::transaction(function () use ($question) {
			$question->post->delete();
			$question->delete();
		});

		return redirect('/')->withSucess('Question deleted!');
	}

	/**
	 * Update a question.
	 */
	public function update(Request $request, string $id)
	{
		$question = Question::findOrFail($id);
		$post = $question->post;
        $tags = Tag::orderBy('name')->get();

		$this->authorize('update', $question);

		$request->validate([
			'title' => 'required|string|max:64',
			'description' => 'required|string|max:10000'
		]);

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

		DB::transaction(function () use ($request, $post, $question) {
			$old_title = $question->title;
			$question->title = $request->title;
			$old_text = $post->text;
			$post->text = $request->description;

			$question->save();
			$post->save();

			Edition::create([
				'post_id' => $question->id,
				'old_title' => $old_title,
				'new_title' => $question->title,
				'old' => $old_text,
				'new' => $request->description,
			]);
		});

		return view('partials.question', [
			'question' => $question,
            'tags' => $tags,
		]);
	}

	public function follow(string $id)
	{
		$user = Auth::user();

		$question = Question::findOrFail($id);
		$this->authorize('show', $question);

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
		DB::transaction(function () use ($user, $question) {
			$exists = DB::table('followquestion')
				->where('user_id', $user->id)
				->where('question_id', $question->id)
				->exists();

			if ($exists) {
				DB::table('followquestion')
					->where('user_id', $user->id)
					->where('question_id', $question->id)
					->delete();
				return;
			}

			DB::table('followquestion')->insert([
				'user_id' => $user->id,
				'question_id' => $question->id
			]);
		});

		return view('partials.follow-btn', [
			'question' => $question
		]);
	}

    /**
     * Add a tag to a question.
     */
    public function addTag(Request $request, $id)
    {
        $request->validate([
            'tag' => 'required|string',
        ]);

        $question = Question::findOrFail($id);
		$this->authorize('tags', $question);

        $tagName = trim($request->tag);
		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        DB::transaction(function () use ($question, $tagName) {
            $tag = Tag::where('name', $tagName)->firstOrFail();
			
			if (!$question->tags->contains($tag->id))
            	$question->tags()->attach($tag->id);
        });

        return redirect()->back()->with('success', 'Tag added successfully.');
    }

    /**
     * Remove a tag from a question.
     */
    public function removeTag(Request $request, $id)
    {
        $request->validate([
            'tag' => 'required|string',
        ]);

        $question = Question::findOrFail($id);
		$this->authorize('tags', $question);

        $tagName = trim($request->tag);
		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        DB::transaction(function () use ($question, $tagName) {
            $tag = Tag::where('name', $tagName)->first();
            if ($tag) {
                $question->tags()->detach($tag->id);
            }
        });

        return redirect()->back()->with('success', 'Tag removed successfully.');
    }
}
