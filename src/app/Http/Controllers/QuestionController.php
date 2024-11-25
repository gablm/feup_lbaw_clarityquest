<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Question;
use App\Models\Edition;
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
		$user = Auth::user();
		$followedQuestions = $user->followedQuestions;

		return view('questions.followed', compact('followedQuestions'));
	}

	public function myQuestions()
	{
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

		$request->validate([
			'title' => 'required|string|max:64|unique:questions,title',  // Ensure unique title for questions
			'description' => 'required|string|max:10000',
		]);

		try {
			$question = DB::transaction(function () use ($request, $user) {
				$post = Post::create([
					'text' => $request->description,
					'user_id' => $user->id
				]);

				return Question::create([
					'title' => $request->title,
					'id' => $post->id
				]);
			});

			return redirect("/questions/{$question->id}")
				->withSuccess('Question created!');
		} catch (\Exception $e) {
			return back()->withErrors(['error' => 'An error occurred while creating the question.']);
		}
	}
	/**
	 * Display a create form.
	 */
	public function showCreateForm()
	{
		if (!Auth::check())
			return redirect('/');

		return view('questions.create');
	}

	/**
	 * Display a question.
	 */
	public function show(string $id)
	{
		$question = Question::findOrFail($id);

		return view('questions.show', [
			'question' => $question
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
			'question' => $question
		]);
	}

	public function follow(string $id)
	{
		$user = Auth::user();

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
		$question = DB::transaction(function () use ($user, $id) {
			$question = Question::findOrFail($id);

			$exists = DB::table('followquestion')
				->where('user_id', $user->id)
				->where('question_id', $question->id)
				->exists();

			if ($exists) {
				DB::table('followquestion')
					->where('user_id', $user->id)
					->where('question_id', $question->id)
					->delete();
				return $question;
			}

			DB::table('followquestion')->insert([
				'user_id' => $user->id,
				'question_id' => $question->id
			]);

			return $question;
		});

		return view('partials.follow-btn', [
			'question' => $question
		]);
	}
}
