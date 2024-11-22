<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 

class UserController extends Controller
{
    /**
     * Show the user's profile.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function profile()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        return view('pages.profile');
    }
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('pages.editprofile');
    }

    /**
     * Update the user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->bio = $request->bio;

        if ($request->has('remove_profile_pic') && $request->remove_profile_pic) {
            if ($user->profile_pic) {
                unlink(public_path($user->profile_pic));
                $user->profile_pic = null;
            }
        } elseif ($request->hasFile('profile_pic')) {
            if ($user->profile_pic && file_exists($user->profile_pic)) {
                unlink(public_path($user->profile_pic));
            }
            $file = $request->file('profile_pic');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('profile_pics'), $filename);
            $user->profile_pic = 'profile_pics/' . $filename;
        }

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated successfully.');
    }

    public function activity()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $comments = $user->commentsOnPosts()->get()->map(function ($comment) {
            $comment->activity_type = 'comment';
            return $comment;
        });

        $answers = $user->answersToQuestions()->get()->map(function ($answer) {
            $answer->activity_type = 'answer';
            return $answer;
        });

        $votes = $user->votesOnPosts()->get()->map(function ($vote) {
            $vote->activity_type = 'vote';
            return $vote;
        });
        /*
        $medals = collect([
            ['type' => 'posts_upvoted', 'count' => $user->postsUpvotedMedals(), 'created_at' => $user->medals->updated_at],
            ['type' => 'posts_created', 'count' => $user->postsCreatedMedals(), 'created_at' => $user->medals->updated_at],
            ['type' => 'questions_created', 'count' => $user->questionsCreatedMedals(), 'created_at' => $user->medals->updated_at],
            ['type' => 'answers_posted', 'count' => $user->answersPostedMedals(), 'created_at' => $user->medals->updated_at],
        ])->map(function ($medal) {
            $medal['activity_type'] = 'medal';
            return (object) $medal;->merge($medals)
        });*/


        $allActivity = $comments->merge($answers)->merge($votes)->sortByDesc('created_at')->take(10);

        return view('home', ['activities' => $allActivity]);
    }

    
    public function showPublicProfile($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Fetch related data
        $questions = $user->questionsCreated()->latest()->get();
        $answers = $user->answersPosted()->latest()->get();

        // Pass data to the view
        return view('pages.public-profile', [
            'user' => $user,
            'questions' => $questions,
            'answers' => $answers,
        ]);
    }
}