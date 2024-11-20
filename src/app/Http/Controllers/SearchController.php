<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        // Validate the query (optional)
        if (empty($query)) {
            return view('pages.search-results', ['results' => [], 'query' => $query]);
        }

        // Perform the search
        $results = Item::where('name', 'LIKE', '%' . $query . '%')
                       ->orWhere('description', 'LIKE', '%' . $query . '%')
                       ->get();

        // Pass the results to the view
        return view('pages.search-results', ['results' => $results, 'query' => $query]);
    }
}
