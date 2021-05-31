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
        $follows = $user->follows()
            ->paginate(Follow::FOLLOWS_PER_PAGE)
            ->withPath(route('ajax.follows.index', $user));

        $followedBy = $user->followedBy()
            ->paginate(Follow::FOLLOWED_BY_PER_PAGE)
            ->withPath(route('ajax.followed-by.index', $user));

        $user->append('date_of_birth');

        return compact('follows', 'followedBy', 'user');
    }
}