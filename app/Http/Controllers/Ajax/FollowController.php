<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\User;

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
        $this->authorize('follow', $user);

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
