<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Question;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Get the search query from the request
        $query = $request->input('search');

        // If the query is empty, return an empty result set
        if (empty($query)) {
            return view('pages.search-results', ['results' => [], 'query' => $query]);
        }

        // Perform a full-text search on the Question table
        $questions = Question::select(
            'id',
            'title',
            DB::raw('ts_rank(tsvectors, plainto_tsquery(\'english\', ?)) as rank') // Compute rank based on search
        )
        ->whereRaw('tsvectors @@ plainto_tsquery(\'english\', ?)', [$query, $query]) // Match the tsvectors column
        ->orderByDesc('rank') // Order by relevance rank
        ->get();

        // Return the search results to the view
        return view('pages.search-results', ['results' => $questions, 'query' => $query]);
    }
}