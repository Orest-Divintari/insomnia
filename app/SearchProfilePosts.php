<?php

namespace App;

use App\ProfilePost;

class SearchProfilePosts
{

    public function query()
    {
        return ProfilePost::search(request('q'));
    }
}