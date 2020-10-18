<?php

namespace App\Search\Algolia;

use App\Search\ProfilePosts;
use App\Search\SearchStrategyInterface;
use Illuminate\Http\Request;

class SearchProfilePosts implements SearchStrategyInterface
{

    /**
     * Search profile posts for the given search query
     *
     * @param Request $request
     * @return Algolia\ScoutExtended\Builder
     */
    public function search(Request $request)
    {
        $searchQuery = $request->input('q');
        return ProfilePosts::search($searchQuery);
    }
}