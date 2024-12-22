<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function followedTags()
    {
        // Check if the user is blocked
        if (Auth::user()->isBlocked())
            return abort(403);

        // Get the authenticated user
        $user = Auth::user();
        // Get the tags followed by the user along with their posts and comments
        $followedTags = $user->followedTags()->with(['posts.user', 'posts.comments'])->get();

        // Return the view with the followed tags
        return view('tags.followed', compact('followedTags'));
    }

    /**
     * Display a tag.
     */
    public function show(string $id)
    {
        // Check if the user is authenticated and blocked
        if (Auth::check() && Auth::user()->isBlocked())
            return abort(403);

        // Find the tag by ID or fail if not found
        $tag = Tag::findOrFail($id);

        // Return the view with the tag
        return view('tags.show', [
            'tag' => $tag
        ]);
    }

    /**
     * Creates a new tag.
     */
    public function create(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();
        // Check if the user has elevated privileges and is not blocked
        if ($user->isElevated() == false || $user->isBlocked())
            return abort(403);

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:24'
        ]);

        // Create a new tag with the provided name
        $tag = Tag::create([
            'name' => $request->name
        ]);

        // Return the partial view with the new tag
        return view('partials.tag-card', [
            'tag' => $tag,
            'panel' => true
        ]);
    }

    /**
     * Delete a tag.
     */
    public function delete(string $id)
    {
        // Find the tag by ID or fail if not found
        $tag = Tag::findOrFail($id);

        // Authorize the user to delete the tag
        $this->authorize('delete', $tag);

        // Delete the tag
        $tag->delete();
    }

    /**
     * Update a tag.
     */
    public function update(Request $request, string $id)
    {
        // Find the tag by ID or fail if not found
        $tag = Tag::findOrFail($id);

        // Authorize the user to update the tag
        $this->authorize('update', $tag);
        
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:64'
        ]);

        // Update the tag name
        $tag->name = $request->name;

        // Save the updated tag
        $tag->save();

        // Return the partial view with the updated tag
        return view('partials.tag-card', [
            'tag' => $tag,
            'panel' => true
        ]);
    }
    
    /**
     * Follow or unfollow a tag.
     */
    public function follow(string $id)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Set the transaction isolation level to REPEATABLE READ
        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        // Start a database transaction
        $tag = DB::transaction(function () use ($user, $id) {
            // Find the tag by ID or fail if not found
            $tag = Tag::findOrFail($id);

            // Check if the user already follows the tag
            $exists = DB::table('followtag')
                ->where('user_id', $user->id)
                ->where('tag_id', $tag->id)
                ->exists();

            // If the user already follows the tag, unfollow it
            if ($exists) {
                DB::table('followtag')
                    ->where('user_id', $user->id)
                    ->where('tag_id', $tag->id)
                    ->delete();
                return $tag;
            }

            // Otherwise, follow the tag
            DB::table('followtag')->insert([
                'user_id' => $user->id,
                'tag_id' => $tag->id
            ]);

            return $tag;
        });

        // Return the partial view with the follow/unfollow button
        return view('partials.follow-tag-btn', [
            'tag' => $tag
        ]);
    }
}