<?php

namespace App\Search\Algolia;

use App\Search\AllPosts;
use App\Search\SearchStrategyInterface;
use Illuminate\Http\Request;

class SearchAllPosts implements SearchStrategyInterface
{

    /**
     * Search all posts for the given search query
     *
     * @param Request $request
     * @return Algolia\ScoutExtended\Builder
     */
    public function search(Request $request)
    {
        $searchQuery = $request->input('q');
        return AllPosts::search($searchQuery);
    }
}