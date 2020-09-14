<?php

namespace App;

use App\Search\AllPosts;

class SearchAllPosts
{

    public function query()
    {
        return AllPosts::search(request('q'));
    }
}