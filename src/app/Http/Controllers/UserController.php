<?php

namespace App\Http\Controllers;

use App\Enum\User\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Providers\RouteServiceProvider;


class UserController extends Controller
{
	/**
	 * Show the user's profile.
	 */
	public function profile()
	{
		if (!Auth::check()) {
			return redirect()->route('login');
		}

		return $this->showPublicProfile(Auth::user()->id);
	}

	/**
	 * Show the form for editing the profile.
	 */
	public function edit()
	{
		return view('users.edit', [
			'user' => Auth::user()
		]);
	}

	/**
	 * Show the form for editing the profile.
	 */
	public function editOther(string $id)
	{
		$user = User::findOrFail($id);
        $this->authorize('update', $user);

		return view('users.edit', [
			'user' => $user
		]);
	}

	/**
	 * Update a user's profile.
	 */
	public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
			'role' => 'string|in:REGULAR,ADMIN,MODERATOR'
        ]);

        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

        DB::transaction(function () use ($request, $user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->bio = $request->bio;

            if ($request->has('remove_profile_pic') && $request->remove_profile_pic) {
                if ($user->profile_pic) {
                    unlink(public_path($user->profile_pic));
                    $user->profile_pic = null;
                }
            } elseif ($request->hasFile('profile_pic')) {
                if ($user->profile_pic && file_exists(public_path($user->profile_pic))) {
                    unlink(public_path($user->profile_pic));
                }

                $file = $request->file('profile_pic');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('profile_pics'), $filename);

                $user->profile_pic = 'profile_pics/' . $filename;
            }

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

			if ($request->has('role'))
			{
				$this->authorize('role', $user);
				$user->role = $request->role;
			}

            $user->save();
        });

        return redirect("/users/{$user->id}")
            ->with('success', 'Profile updated successfully.');
    }

	public function activity()
	{
		if (!Auth::check())
			return redirect()->route('login');

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

	public function showPublicProfile(string $id)
	{
		// Fetch the user by ID
		$user = User::findOrFail($id);

		// Fetch related data
		$questions = $user->questionsCreated()->latest()->get();
		$answers = $user->answersPosted()->latest()->get();

		// Pass data to the view
		return view('users.profile', [
			'user' => $user,
			'questions' => $questions,
			'answers' => $answers,
		]);
	}

	/**
	 * Delete a user.
	 */
	public function delete(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        $curr = Auth::user();

        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

        DB::transaction(function () use ($user, $curr, $request) {
            $user->delete();

            if ($curr->id == $user->id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        });

        if ($curr->id == $user->id) {
            return redirect(RouteServiceProvider::HOME)
                ->withSuccess('You have logged out successfully!');
        }
    }

	/**
	 * Blocks a user.
	 */
	public function block(string $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('block', $user);

        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        DB::transaction(function () use ($user) {
            $user->role = $user->role == Permission::Blocked 
				? Permission::Regular : Permission::Blocked;
			$user->save();
        });

		return view('partials.user-card', [
			'user' => $user,
			'panel' => true
		]);
    }

	/**
	 * Create a user.
	 */
	public function create(Request $request)
    {
        if (Auth::user()->isAdmin() == false)
			return abort(403);

		$request->validate([
			'username' => 'required|string|max:32|unique:users',
			'name' => 'required|string|max:250',
			'email' => 'required|email|max:250|unique:users',
			'password' => 'required|min:8',
			'role' => 'required|string|in:REGULAR,ADMIN,MODERATOR'
		]);

        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
				'username' => $request->username,
				'name' => $request->name,
				'email' => $request->email,
				'password' => Hash::make($request->password), 
				'role' => $request->role
			]);

			return $user;
        });

        return view('partials.user-card', [
			'user' => $user,
			'panel' => true
		]);;
    }
}
