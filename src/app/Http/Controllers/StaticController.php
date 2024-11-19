<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Post;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Comment;
use App\Models\Vote;
use App\Models\Tag;
use App\Models\Notification;

class StaticController extends Controller
{
    /**
     * Show the application home page.
     *
     * @return \Illuminate\View\View
     */
    
    public function index()
    {
        $topQuestions = Question::getTopQuestions();
        $latestQuestions = Question::getLatestQuestions();

        return view('pages.home', compact('topQuestions', 'latestQuestions'));
    }
}