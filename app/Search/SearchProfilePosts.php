<?php

namespace App\Search;

use App\Search\ProfilePosts;
use App\Search\SearchIndexInterface;

class SearchProfilePosts implements SearchIndexInterface
{

    /**
     * Search profile posts for the given search query
     *
     * @param mixed $searchQuery
     * @return Algolia\ScoutExtended\Builder
     */
    public function search($searchQuery)
    {
        return ProfilePosts::search($searchQuery);
    }
}