<?php

namespace App\Search;

use App\Search\AllPosts;
use App\Search\SearchIndexInterface;

class SearchAllPosts implements SearchIndexInterface
{
    /**
     * Search all posts for the given search query
     *
     * @param mixed $searchQuery
     * @return Algolia\ScoutExtended\Builder
     */
    public function search($searchQuery)
    {
        return AllPosts::search($searchQuery);
    }
}