<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;

class FollowingsController extends Controller
{
    /**
     * Get the following users for the user
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        return $user->followings()
            ->withProfileInfo(auth()->user())
            ->paginate(Follow::FOLLOWINGS_PER_PAGE);
    }
}
