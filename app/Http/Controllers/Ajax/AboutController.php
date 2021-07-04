<?php

namespace App\Http\Controllers\Ajax;

use App\Follow;
use App\Http\Controllers\Controller;
use App\User;

class AboutController extends Controller
{
    /**
     * Get the About information of user
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $followings = $user->followings()
            ->withProfileInfo(auth()->user())
            ->paginate(Follow::FOLLOWINGS_PER_PAGE)
            ->withPath(route('ajax.followings.index', $user));

        $followers = $user->followers()
            ->withProfileInfo(auth()->user())
            ->paginate(Follow::FOLLOWERS_BY_PER_PAGE)
            ->withPath(route('ajax.followers.index', $user));

        $user->append('date_of_birth');

        return compact('followings', 'followers', 'user');
    }
}