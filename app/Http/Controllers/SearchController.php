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
    }
}