<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\User;

class IsFollowedByAuthUserController extends Controller
{
    public function show(User $user)
    {
        return ['is_followed' => auth()->user()->following($user)];
    }
}
