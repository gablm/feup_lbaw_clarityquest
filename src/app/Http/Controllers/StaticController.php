<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Question;
use App\Models\Report;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaticController extends Controller
{
    /**
     * Show the application home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $topQuestions = Question::getTopQuestions();
        $latestQuestions = Question::getLatestQuestions();

        return view('pages.home', compact('topQuestions', 'latestQuestions'));
    }

    /**
     * Show the about page.
     *
     * @return \Illuminate\View\View
     */
    public function aboutUs()
    {
        return view('pages.about');
    }

    public function contacts()
    {
        return view('pages.contacts');
    }

	public function search(Request $request)
	{
		// Get the search query from the request
		$query = $request->input('search');

		// If the query is empty, return an empty result set
		if (empty($query)) {
			return view('pages.search-results', ['results' => [], 'query' => $query]);
		}

		$modifiedQuery = implode(
			' | ',
			array_map(fn($term) => $term . ':*', explode(' ', $query))
		);

		// Perform a full-text search on the Question table
		$questions = Question::select(
			'id',
			'title',
			DB::raw('ts_rank(tsvectors, websearch_to_tsquery(\'english\', :query)) as rank') // Compute rank based on search
		)
			->whereRaw('tsvectors @@ websearch_to_tsquery(\'english\', :query)', ['query' => $modifiedQuery]) // Match the tsvectors column
			->orderByDesc('rank') // Order by relevance rank
			->get();

		$users = User::select(
			'id',
			'username',
			'name',
			'profile_pic',
			DB::raw('ts_rank(tsvectors, websearch_to_tsquery(\'english\', :query)) as rank') // Compute rank based on search
		)
			->whereRaw('tsvectors @@ websearch_to_tsquery(\'english\', :query)', ['query' => $modifiedQuery]) // Match the tsvectors column
			->orderByDesc('rank') // Order by relevance rank
			->get();

		// Return the search results to the view
		return view('pages.search', ['questions' => $questions, 'users' => $users, 'query' => $query]);
	}

	public function admin()
	{
		if (!Auth::check() || !Auth::user()->isElevated())
            return redirect('/');

		$users = User::all();

		$reports = Report::all();
		$tags = Tag::all();

		// Pass data to the view
		return view('pages.admin', [
			'users' => $users,
			'reports' => $reports,
			'tags' => $tags,
		]);
	}
}