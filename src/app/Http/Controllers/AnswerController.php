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
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
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

		return view('partials.answer', [
			'answer' => $answer
		]);
	}

	/**
     * Creates a new answer.
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'text' => 'required|string|max:10000',
            'id' => 'required|integer|exists:questions,id',
        ]);

        $question = Question::findOrFail($request->id);

        try {
            $answer = DB::transaction(function () use ($request, $user, $question) {
                $post = Post::create([
                    'text' => $request->text,
                    'user_id' => $user->id,
                ]);

                Notification::create([
                    'receiver' => $question->post->user_id, // Original poster's ID
                    'description' => "Your question titled '{$question->title}' has been answered by user '{$user->username}'.",
                    'type' => 'RESPONSE',
                ]);

                return Answer::create([
                    'id' => $post->id,
                    'question_id' => $question->id,
                ]);
            });

            return view('partials.answer', ['answer' => $answer]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred while creating the answer.']);
        }
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
			'text' => 'required|string|max:10000'
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
        });

        return redirect()->back()->with('success', 'Answer marked as correct.');
    }
}