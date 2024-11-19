<?php

namespace App\Http\Controllers;

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
        $followedQuestions = $user->followedQuestions()->with(['post.user', 'answers', 'post.comments', 'tags'])->get();

        return view('followedquestions', compact('followedQuestions'));
    }
    public function myQuestions()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $myQuestions = $user->questionsCreated()->with(['post.user', 'answers', 'post.comments', 'tags'])->get();

        return view('myquestions', compact('myQuestions'));
    }
}