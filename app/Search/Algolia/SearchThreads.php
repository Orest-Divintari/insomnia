<?php
namespace App\Search\Algolia;

use App\Search\SearchStrategyInterface;
use App\Search\Threads;
use Illuminate\Http\Request;

class SearchThreads implements SearchStrategyInterface
{

    /**
     * Search threads and replies for the given search query
     *
     * @param Request $request
     * @return Algolia\ScoutExtended\Builder
     */
    public function search(Request $request)
    {
        $searchQuery = $request->input('q');
        return Threads::search($searchQuery);
    }
}