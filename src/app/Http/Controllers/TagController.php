<?php

namespace App\Http\Controllers;

use App\Models\Tag;
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

        return view('tags.followed', compact('followedTags'));
    }

	/**
     * Display a comment.
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
			'tag' => $tag
		]);
    }

	/**
     * Delete a comment.
     */
	public function delete(string $id)
	{
		$tag = Tag::findOrFail($id);

		$this->authorize('delete');

		$tag->delete();
	}

	/**
     * Update a comment.
     */
    public function update(Request $request, string $id)
    {
		$tag = Tag::findOrFail($id);

		$this->authorize('update');

        $request->validate([
			'name' => 'required|string|max:64'
        ]);

		$tag->name = $request->name;

		$tag->save();

        return view('partials.tag-card', [
			'tag' => $tag
		]);
    }
}