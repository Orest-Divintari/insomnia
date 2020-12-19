<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;

class FollowController extends Controller
{
    /**
     * Store a new follow
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function store(User $user)
    {
        auth()->user()->follow($user);
        return response('Following user', 200);
    }

    /**
     * Remove an existing follow
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        auth()->user()->unfollow($user);
        return response('Unfollowed user', 200);
    }

}