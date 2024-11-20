<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('pages.flwquest', compact('followedQuestions'));
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
			'title' => 'required|string|max:64',
            'description' => 'required|string|max:2000'
        ]);

		$post = Post::create([
			'text' => $request->description,
			'user_id' => $user->id
		]);

        $question = Question::create([
			'title' => $request->title,
            'id' => $post->id
        ]);

        return redirect("/questions/$question->id")
			->withSuccess('Question created!');
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

	public function show(string $id)
	{
		$user_id = Auth::user()->id ?? -2;

		$question = Question::findOrFail($id);
		$question_owner = $question->post->user->id ?? -1;

		return view('questions.show', [
			'question' => $question,
			'mine' => $question_owner == $user_id
		]);
	}
    
}
