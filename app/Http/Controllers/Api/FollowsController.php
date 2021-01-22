<?php

namespace App\Http\Controllers\Api;

use App\Follow;
use App\Http\Controllers\Controller;
use App\User;

class FollowsController extends Controller
{
    /**
     * Get the following users for the user
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        return $user->follows()
            ->withProfileInfo()
            ->paginate(Follow::FOLLOWS_PER_PAGE);
    }
}