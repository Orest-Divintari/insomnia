<?php

namespace App\Http\Controllers;

use App\Search;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Number of results per page
     * @var int
     */
    const RESULTS_PER_PAGE = 2;

    /**
     * Display the search results
     *
     * @return void
     */
    public function show(Search $search)
    {
        if (request()->expectsJson()) {
            return $search->getResults();
        }

        $results = $search->getResults();
        $query = request('q');
        return view('search.show', compact('results', 'query'));
    }

    /**
     * Display the advanced search form
     *
     * @return void
     */
    public function create()
    {
        return $this->getSearchType();
    }

    /**
     * Determine the search type
     *
     * @return View
     */
    public function getSearchType()
    {
        if (request('type') == 'thread') {
            return view('search.advanced.threads', ['type' => 'thread']);
        } elseif (request('type') == 'profile_post') {
            return view('search.advanced.profile_posts', ['type' => 'profile_post']);
        } elseif (request('type') == 'tags') {
            return view('search.advanced.tags', ['type' => 'tags']);
        }
        return view('search.advanced.all_posts', ['type' => '']);
    }
}