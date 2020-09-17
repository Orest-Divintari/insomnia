<?php

namespace App;

use App\Filters\AllPostsFilter;
use App\Search\AllPosts;

class SearchAllPosts
{

    public function query()
    {
        return app(AllPostsFilter::class)->apply(
            AllPosts::search(request('q'))
        );
    }

}