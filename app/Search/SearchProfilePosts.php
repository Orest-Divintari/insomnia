<?php

namespace App\Search;

use App\Search\ProfilePosts;

class SearchProfilePosts
{

    public function query()
    {
        $filters = app('ProfilePostFilters');
 
        return $filters->apply(
            ProfilePosts::search(request('q'))
        );
    }
}