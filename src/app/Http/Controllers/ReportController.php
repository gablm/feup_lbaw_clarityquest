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
			'name' => 'required|string|max:64',
			'id' => 'required|integer|exists:posts,id'
        ]);

		$post = Post::findOrFail($request->post_id);

		Report::create([
			'reason' => $request->reason,
			'user_id' => Auth::user()->id,
			'post_id' => $post->id
		]);
    }

    public function delete(Request $request, string $id)
    {
		$report = Report::findOrFail($id);

        $this->authorize('delete', $report);

		$report->delete();
    }
}
