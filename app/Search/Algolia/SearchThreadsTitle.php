<?php

namespace App\Search\Algolia;

use App\Search\SearchStrategyInterface;
use App\Thread;
use Illuminate\Http\Request;

class SearchThreadsTitle implements SearchStrategyInterface
{

    /**
     * Search threads by title for the given search query
     *
     * @param Request $request
     * @return Algolia\ScoutExtended\Builder
     */
    public function search(Request $request)
    {
        $searchQuery = $request->input('q');
        return Thread::search($searchQuery);
    }
}