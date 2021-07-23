<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\User;

class IsFollowedByAuthUserController extends Controller
{
    /**
     * Determine whether the given user is followed by the authenticated user
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return ['is_followed' => auth()->user()->following($user)];
    }
}
