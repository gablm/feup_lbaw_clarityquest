<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function followedTags()
    {
        $user = Auth::user();
        $followedTags = $user->followedTags()->with(['posts.user', 'posts.comments'])->get();

        return view('followedtags', compact('followedTags'));
    }
}