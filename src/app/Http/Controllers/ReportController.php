<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function create(Request $request)
    {
		$request->validate([
			'reason' => 'required|string|max:100',
			'id' => 'required|integer|exists:posts,id'
        ]);

		if (Auth::user()->isBlocked())
			return abort(403);

		$post = Post::findOrFail($request->id);

		Report::create([
			'reason' => $request->reason,
			'user_id' => Auth::user()->id,
			'post_id' => $post->id
		]);

		return response()->json(['success' => true]);
    }

    public function delete(Request $request, string $id)
    {
		$report = Report::findOrFail($id);

        $this->authorize('delete', $report);

		$report->delete();
    }
}
