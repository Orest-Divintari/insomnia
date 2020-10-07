<?php

namespace App\Search;

use App\Activity;

class SearchProfilePosts
{

    /**
     * Search profile posts and comments
     *
     * @return mixed
     */
    public function query()
    {
        $filters = app('ProfilePostFilters');
        $searchQuery = request('q');

        if (isset($searchQuery)) {
            $query = ProfilePosts::search($searchQuery);
        } else {
            $query = Activity::ofProfilePostsAndComments();
        }
        return $filters->apply($query);
    }
}