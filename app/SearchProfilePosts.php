<?php

namespace App;

use App\Search\ProfilePosts;

class SearchProfilePosts
{

    public function query()
    {
        return ProfilePosts::search(request('q'));
    }
}