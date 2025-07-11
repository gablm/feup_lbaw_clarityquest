<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Post;
use App\Models\Question;
use App\Models\Edition;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use \Exception;

class AnswerController extends Controller
{
	/**
	 * Display a listing of the user's answers.
	 *
	 * @return \Illuminate\View\View\Illuminate\Http\RedirectResponse
	 */
	public function myAnswers()
	{
		if (!Auth::check())
			return redirect()->route('login');

		$user = Auth::user();
		$answers = $user->answersPosted()->with('question')->get();

		return view('answers.mine', compact('answers'));
	}

	/**
	 * Display a answer.
	 */
	public function show(string $id)
	{
		$answer = Answer::findOrFail($id);

		$this->authorize('show', $answer);
		$user = Auth::user();
		$voteStatus = null;

		if ($user) {
			$vote = $answer->post->votes()->where('user_id', $user->id)->first();
			$voteStatus = $vote ? ($vote->positive ? 'positive' : 'negative') : null;
		}

		$voteStatus = $vote ? ($vote->positive ? 'positive' : 'negative') : null;

		return view('partials.answer', [
			'answer' => $answer,
			'voteStatus' => $voteStatus
		]);
	}

	/**
	 * Creates a new answer.
	 */
	public function create(Request $request)
	{
		$user = Auth::user();

		if ($user->isBlocked())
			return abort(403);

		$request->validate([
			'text' => 'required|string|max:500',
			'id' => 'required|integer|exists:questions,id',
		]);

		// Find the question that will "own" the answer.
		$question = Question::findOrFail($request->id);

		$answer = DB::transaction(function () use ($request, $user, $question) {
			// Create the post associated with the answer
			$post = Post::create([
				'text' => $request->text,
				'user_id' => $user->id,
			]);

			// Send notification to the owner of the question if not null (account was not deleted)
			if ($question->post->user_id != null) {
				$notification = Notification::create([
					'receiver' => $question->post->user_id,
					'description' => "Your question titled '{$question->title}' has been answered by user '{$user->username}'.",
					'type' => 'RESPONSE',
				]);

				DB::table('notificationpost')->insert([
					'notification_id' => $notification->id,
					'post_id' => $question->id
				]);
			}

			// Send notification to all question followers
			foreach ($question->follows as $follower) {
				$notification = Notification::create([
					'receiver' => $follower->id,
					'description' => "The question titled '{$question->title}' you follow just received a answer by '{$user->username}'.",
					'type' => 'RESPONSE',
				]);

				DB::table('notificationpost')->insert([
					'notification_id' => $notification->id,
					'post_id' => $question->id
				]);
			}

			// Create answer
			return Answer::create([
				'id' => $post->id,
				'question_id' => $question->id,
			]);
		});

		return view('partials.answer', ['answer' => $answer]);
	}


	/**
	 * Delete a answer.
	 */
	public function delete(string $id)
	{
		$answer = Answer::findOrFail($id);
		$this->authorize('delete', $answer);

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

		DB::transaction(function () use ($answer) {
			$answer->post->delete();
			$answer->delete();
		});

		return;
	}

	/**
	 * Update a answer.
	 */
	public function update(Request $request, string $id)
	{
		$answer = Answer::findOrFail($id);
		$post = $answer->post;

		$this->authorize('update', $answer);

		$request->validate([
			'text' => 'required|string|max:500'
		]);

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

		DB::transaction(function () use ($request, $post, $answer) {
			$old_text = $post->text;
			$post->text = $request->text;

			$post->save();
			$answer->save();

			Edition::create([
				'post_id' => $answer->id,
				'old_title' => null,
				'new_title' => null,
				'old' => $old_text,
				'new' => $request->text,
			]);
		});

		return view('partials.answer', [
			'answer' => $answer
		]);
	}
	/**
	 * Mark an answer as correct.
	 */
	public function markAsCorrect(string $id)
	{
		$answer = Answer::findOrFail($id);
		$question = $answer->question;

		$this->authorize('update', $question);

		DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

		DB::transaction(function () use ($answer, $question) {
			$question->answers()->update(['correct' => false]);

			$answer->correct = true;
			$answer->save();
			
			// Send notification to the owner of the answer if not null (account was not deleted)
			if ($answer->post->user_id != null) {
				$notification = Notification::create([
					'receiver' => $answer->post->user_id,
					'description' => "Your answer on question '{$question->title}' has been marked as correct!",
					'type' => 'RESPONSE',
				]);

				DB::table('notificationpost')->insert([
					'notification_id' => $notification->id,
					'post_id' => $question->id
				]);
			}
		});

		return view('partials.answer-list', [
			'answerList' => $question->answers
		]);
	}
}
