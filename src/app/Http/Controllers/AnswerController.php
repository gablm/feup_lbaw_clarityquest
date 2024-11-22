<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Post;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnswerController extends Controller
{
    /**
     * Display a listing of the user's answers.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function myAnswers()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

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
			'id' => 'required|string'
        ]);

		$question = Question::findOrFail($request->id);

		$post = Post::create([
			'text' => $request->text,
			'user_id' => $user->id
		]);

        $answer = Answer::create([
            'id' => $post->id,
			'question_id' => $question->id
        ]);

        return view('partials.answer', ['answer' => $answer]);
    }

	/**
     * Delete a answer.
     */
	public function delete(string $id)
	{
		$answer = Answer::findOrFail($id);

		$this->authorize('delete', $answer);

		$answer->delete();

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

		$post->text = $request->text;

		$post->save();
		$answer->save();

        return view('partials.answer', [
			'answer' => $answer
		]);
    }
}