<?php

namespace App\Search;

use App\Models\Thread;
use App\Search\SearchIndexInterface;

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
