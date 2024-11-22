<?php

namespace App\Http\Controllers;

use App\Models\Answer;
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
}