<?php

namespace App\Search;

class SearchAllPosts
{

    public function query()
    {
        $filters = app('AllPostsFilters');

        return $filters->apply(
            AllPosts::search(request('q'))
        );
    }

}