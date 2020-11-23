<?php

namespace App\Http\Controllers\Api;

use App\Activity;
use App\Http\Controllers\Controller;
use App\User;

class PostingActivityController extends Controller
{
    /**
     * Fetch the postings of the user
     *
     * @param User $user
     * @return
     */
    public function index(User $user)
    {
        return Activity::feedPosts($user)
            ->paginate(Activity::NUMBER_OF_ACTIVITIES);
    }
}