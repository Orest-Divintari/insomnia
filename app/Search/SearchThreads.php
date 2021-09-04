<?php
namespace App\Search;

use App\Search\SearchIndexInterface;
use App\Search\Threads;

class SearchThreads implements SearchIndexInterface
{

    /**
     * Search threads and replies for the given search query
     *
     * @param mixed $searchQuery
     * @return Laravel\Scout\Builder
     */
    public function search($searchQuery)
    {
        return Threads::search($searchQuery);
    }
}