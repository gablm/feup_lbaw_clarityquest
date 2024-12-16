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
		$query = $request->input('search');
		$filter = $request->input('filter', 'all'); 
		$sort = $request->input('sort', 'none'); 

		if (empty($query)) {
			return view('pages.search-results', ['results' => [], 'query' => $query]);
		}

		$modifiedQuery = implode(
			' | ',
			array_map(fn($term) => $term . ':*', explode(' ', $query))
		);

		// Initialize results
		$questions = collect();
		$users = collect();
		$tags = collect();

		// Apply filters
		if ($filter === 'all' || $filter === 'questions') {
			$questions = Question::select(
				'questions.id',
				'questions.title',
				DB::raw('posts.created_at, posts.votes, ts_rank(tsvectors, websearch_to_tsquery(\'english\', :query)) as rank'),
				DB::raw('(SELECT COUNT(*) FROM answers WHERE answers.question_id = questions.id) as answers_count')
			)
			->join('posts', 'posts.id', '=', 'questions.id')  
			->whereRaw('tsvectors @@ websearch_to_tsquery(\'english\', :query)', ['query' => $modifiedQuery])
			->when($sort === 'newest', function ($query) {
				$query->orderByDesc('posts.created_at');
			})
			->when($sort === 'oldest', function ($query) {
				$query->orderBy('posts.created_at');
			})
			->when($sort === 'alphabetical', function ($query) {
				$query->orderBy('questions.title');
			})
			->when($sort === 'most_upvoted', function ($query) {
				$query->orderByDesc(DB::raw('posts.votes')); 
			})
			->when($sort === 'least_upvoted', function ($query) {
				$query->orderBy(DB::raw('posts.votes')); 
			})
			->orderByDesc('rank') 
			->get();
		}

		if ($filter === 'all' || $filter === 'users') {
			$users = User::select(
				'id',
				'username',
				'name',
				'profile_pic',
				DB::raw('ts_rank(tsvectors, websearch_to_tsquery(\'english\', :query)) as rank')
			)
			->whereRaw('tsvectors @@ websearch_to_tsquery(\'english\', :query)', ['query' => $modifiedQuery])
			->when($sort === 'newest', fn($q) => $q->orderByDesc('created_at'))
			->when($sort === 'oldest', fn($q) => $q->orderBy('created_at'))
			->orderByDesc('rank')
			->get();
		}

		if ($filter === 'all' || $filter === 'tags') {
			$tags = Tag::select(
				'id',
				'name',
				DB::raw('ts_rank(tsvectors, websearch_to_tsquery(\'english\', :query)) as rank')
			)
			->whereRaw('tsvectors @@ websearch_to_tsquery(\'english\', :query)', ['query' => $modifiedQuery])
			->when($sort === 'alphabetical', fn($q) => $q->orderBy('name'))
			->when($sort === 'newest', fn($q) => $q->orderByDesc('created_at'))
			->when($sort === 'oldest', fn($q) => $q->orderBy('created_at'))
			->orderByDesc('rank')
			->get();
		}

		// Return the search results to the view
		return view('pages.search', [
			'questions' => $questions,
			'users' => $users,
			'tags' => $tags,
			'query' => $query,
			'filter' => $filter,
			'sort' => $sort,
		]);
	}
	public function admin()
	{
		if (!Auth::check() || !Auth::user()->isElevated())
			return redirect('/');

		$users = User::all();

		$reports = Report::orderBy('created_at', 'DESC')->get();
		$tags = Tag::all();

		// Pass data to the view
		return view('pages.admin', [
			'users' => $users,
			'reports' => $reports,
			'tags' => $tags,
		]);
	}

	 /**
     * Show the main features page.
     */
    public function mainFeatures()
    {
        return view('pages.main-features');
    }
}
