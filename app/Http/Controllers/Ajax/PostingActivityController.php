<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;

class PostingActivityController extends Controller
{
    /**
     * Fetch the postings of the user
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        return Activity::feedPosts($user)
            ->paginate(Activity::NUMBER_OF_ACTIVITIES);
    }
}
