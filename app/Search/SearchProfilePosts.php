<?php

namespace App\Search;

use App\Search\ProfilePosts;
use App\Search\SearchIndexInterface;

class SearchProfilePosts implements SearchIndexInterface
{

    /**
     * Search profile posts for the given search query
     *
     * @param string $searchQuery
     * @return Algolia\ScoutExtended\Builder
     */
    public function search(string $searchQuery)
    {
        return ProfilePosts::search($searchQuery);
    }
}