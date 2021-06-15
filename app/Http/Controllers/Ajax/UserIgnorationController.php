<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\User;

class UserIgnorationController extends Controller
{
    /**
     * Mark user as ignored
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function store(User $user)
    {
        $user->markAsIgnored();

        return response('The user has been ignored successfully.', 200);
    }

    /**
     * Mark user as unignored
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->markAsUnignored();

        return response('The user has been unignored successfully.', 200);
    }
}