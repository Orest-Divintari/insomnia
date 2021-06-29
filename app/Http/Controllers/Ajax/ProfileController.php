<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\User;

class ProfileController extends Controller
{

    public function show($username)
    {
        return User::findByName($username)
            ->withProfileInfo(auth()->user())
            ->first()
            ->append('join_date')
            ->append('permissions');
    }
}