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
			'tag' => $tag
		]);
    }

	/**
     * Delete a tag.
     */
	public function delete(string $id)
	{
		//$this->authorize('delete');

		$tag = Tag::findOrFail($id);
		$tag->delete();
	}

	/**
     * Update a tag.
     */
    public function update(Request $request, string $id)
    {
		//$this->authorize('update');
		
		$tag = Tag::findOrFail($id);
		
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