<?php

namespace App\Http\Controllers\Ajax;

use App\Follow;
use App\Http\Controllers\Controller;
use App\User;

class FollowedByController extends Controller
{
    /**
     * Get the followers for the user
     *
     * @param User $user
     * @return \Illuminate\http\Response
     */
    public function index(User $user)
    {
        return $user->unignoredFollowers()
            ->withProfileInfo()
            ->paginate(Follow::FOLLOWED_BY_PER_PAGE);
    }
}