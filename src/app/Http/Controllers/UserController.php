<?php

namespace App\Http\Controllers;

use App\Enum\User\Permission;
use App\Models\Medals;
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
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Show the public profile of the authenticated user
        return $this->showPublicProfile(Auth::user()->id);
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        // Check if the user is blocked
        if (Auth::user()->isBlocked())
            return abort(403);

        // Return the edit profile view with the authenticated user's data
        return view('users.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Show the form for editing another user's profile.
     */
    public function editOther(string $id)
    {
        // Find the user by ID or fail if not found
        $user = User::findOrFail($id);
        // Authorize the user to update the profile
        $this->authorize('update', $user);

        // Return the edit profile view with the specified user's data
        return view('users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update a user's profile.
     */
    public function update(Request $request, string $id)
    {
        // Find the user by ID or fail if not found
        $user = User::findOrFail($id);

        // Authorize the user to update the profile
        $this->authorize('update', $user);

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'string|in:BLOCKED,REGULAR,ADMIN,MODERATOR'
        ]);

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

        // Start a database transaction
        DB::transaction(function () use ($request, $user) {
            // Update the user's name, email, and bio
            $user->name = $request->name;
            $user->email = $request->email;
            $user->bio = $request->bio;

            // Handle profile picture removal
            if ($request->has('remove_profile_pic') && $request->remove_profile_pic) {
                if ($user->profile_pic) {
                    unlink(public_path($user->profile_pic));
                    $user->profile_pic = null;
                }
            } elseif ($request->hasFile('profile_pic')) {
                // Handle profile picture upload
                if ($user->profile_pic && file_exists(public_path($user->profile_pic))) {
                    unlink(public_path($user->profile_pic));
                }

                $file = $request->file('profile_pic');
                $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('profile_pics'), $filename);

                $user->profile_pic = 'profile_pics/' . $filename;
            }

            // Update the user's password if provided
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            // Update the user's role if provided
            if ($request->has('role')) {
                $this->authorize('role', $user);
                $user->role = $request->role;
            }

            // Save the updated user
            $user->save();
        });

        // Redirect to the user's profile with a success message
        return redirect("/users/{$user->id}")
            ->with('success', 'Profile updated successfully.');
    }

    public function showPublicProfile(string $id)
    {
        // Check if the user is authenticated and blocked
        if (Auth::check() && Auth::user()->isBlocked())
            return abort(403);

        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Fetch related data
        $questions = $user->questionsCreated()->latest()->get();
        $answers = $user->answersPosted()->latest()->get();
        $medals = $user->medals;

        // Pass data to the view
        return view('users.profile', [
            'user' => $user,
            'questions' => $questions,
            'answers' => $answers,
            'medals' => $medals
        ]);
    }

    /**
     * Delete a user.
     */
    public function delete(Request $request, string $id)
    {
        // Find the user by ID or fail if not found
        $user = User::findOrFail($id);

        // Authorize the user to delete the profile
        $this->authorize('delete', $user);

        // Get the current authenticated user
        $curr = Auth::user();

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');

        // Start a database transaction
        DB::transaction(function () use ($user, $curr, $request) {
            // Delete the user
            $user->delete();

            // If the current user is deleting their own profile, log them out
            if ($curr->id == $user->id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        });

        // If the current user is deleting their own profile, redirect to the home page with a success message
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
        // Find the user by ID or fail if not found
        $user = User::findOrFail($id);

        // Authorize the user to block the profile
        $this->authorize('block', $user);

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        // Start a database transaction
        DB::transaction(function () use ($user) {
            // Toggle the user's role between blocked and regular
            $user->role = $user->role == Permission::Blocked
                ? Permission::Regular : Permission::Blocked;
            // Save the updated user
            $user->save();
        });

        // Return the partial view with the updated user card
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
        // Check if the authenticated user is an admin
        if (Auth::user()->isAdmin() == false)
            return abort(403);

        // Validate the request data
        $request->validate([
            'username' => 'required|string|max:32|unique:users',
            'name' => 'required|string|max:250',
            'email' => 'required|email|max:250|unique:users',
            'password' => 'required|min:8',
            'role' => 'required|string|in:BLOCKED,REGULAR,ADMIN,MODERATOR'
        ]);

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        // Start a database transaction
        $user = DB::transaction(function () use ($request) {
            // Create a new user with the provided data
            $user = User::create([
                'username' => $request->username,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role
            ]);

            // Create medals for the new user
            Medals::create(['user_id' => $user->id]);

            return $user;
        });

        // Return the partial view with the new user card
        return view('partials.user-card', [
            'user' => $user,
            'panel' => true
        ]);
    }

    public function showMedals(string $id)
    {
        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Fetch the medals information
        $medals = $user->medals;

        // Pass data to the view
        return view('partials.medals', [
            'user' => $user,
            'medals' => $medals,
        ]);
    }
}