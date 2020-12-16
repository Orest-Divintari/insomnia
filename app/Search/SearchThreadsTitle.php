<?php

namespace App\Search;

use App\Search\SearchIndexInterface;
use App\Thread;

class SearchThreadsTitle implements SearchIndexInterface
{

    /**
     * Search threads by title for the given search query
     *
     * @param mixed $searchQuery
     * @return Algolia\ScoutExtended\Builder
     */
    public function search($searchQuery)
    {
        return Thread::search($searchQuery);
    }
}