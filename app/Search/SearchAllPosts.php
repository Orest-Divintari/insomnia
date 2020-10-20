<?php

namespace App\Search;

use App\Search\AllPosts;
use App\Search\SearchIndexInterface;

class SearchAllPosts implements SearchIndexInterface
{
    /**
     * Search all posts for the given search query
     *
     * @param string $searchQuery
     * @return Algolia\ScoutExtended\Builder
     */
    public function search(string $searchQuery)
    {
        return AllPosts::search($searchQuery);
    }
}