<?php

namespace App;

use App\Filters\ProfilePostFilters;
use App\Search\ProfilePosts;

class SearchProfilePosts
{
    protected $filters;

    public function __construct()
    {
        $this->filters = app(ProfilePostFilters::class);
    }

    public function query()
    {
        return $this->filters->apply(
            ProfilePosts::search(request('q'))
        );
    }
}