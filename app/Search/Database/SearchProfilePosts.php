<?php

namespace App\Search\Database;

use App\Activity;
use App\Search\SearchStrategyInterface;
use Illuminate\Http\Request;

class SearchProfilePosts implements SearchStrategyInterface
{

    /**
     * Get the activity of profile posts and comments
     *
     * @param Request $request
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function search(Request $request)
    {
        return Activity::ofProfilePostsAndComments();
    }
}