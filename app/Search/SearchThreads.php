<?php
namespace App\Search;

use App\Search\SearchIndexInterface;
use App\Search\Threads;

class SearchThreads implements SearchIndexInterface
{

    /**
     * Search threads and replies for the given search query
     *
     * @param string $searchQuery
     * @return Algolia\ScoutExtended\Builder
     */
    public function search(string $searchQuery)
    {
        return Threads::search($searchQuery);
    }
}