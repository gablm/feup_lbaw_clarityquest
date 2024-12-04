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
        $user = Auth::user();
        $followedTags = $user->followedTags()->with(['posts.user', 'posts.comments'])->get();

        return view('tags.followed', compact('followedTags'));
    }

	/**
     * Display a tag.
     */
    public function show(string $id)
    {
        $tag = Tag::findOrFail($id);

		return view('tags.show', [
			'tag' => $tag
		]);
    }

	/**
     * Creates a new tag.
     */
    public function create(Request $request)
    {
		//$this->authorize('create');

        $request->validate([
			'name' => 'required|string|max:64'
        ]);

		$tag = Tag::create([
			'name' => $request->name
		]);

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
		$tag = Tag::findOrFail($id);

		$this->authorize('delete', $tag);

		$tag->delete();
	}

	/**
     * Update a tag.
     */
    public function update(Request $request, string $id)
    {
		$tag = Tag::findOrFail($id);

		$this->authorize('update', $tag);
		
        $request->validate([
			'name' => 'required|string|max:64'
        ]);

		$tag->name = $request->name;

		$tag->save();

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
        $user = Auth::user();

        DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        $tag = DB::transaction(function () use ($user, $id) {
            $tag = Tag::findOrFail($id);

            $exists = DB::table('followtag')
                ->where('user_id', $user->id)
                ->where('tag_id', $tag->id)
                ->exists();

            if ($exists) {
                DB::table('followtag')
                    ->where('user_id', $user->id)
                    ->where('tag_id', $tag->id)
                    ->delete();
                return $tag;
            }

            DB::table('followtag')->insert([
                'user_id' => $user->id,
                'tag_id' => $tag->id
            ]);

            return $tag;
        });

        return view('partials.follow-tag-btn', [
			'tag' => $tag
		]);
    }
}