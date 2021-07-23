<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\User;

class ProfileController extends Controller
{

    /**
     * Get the profile information of the given username
     *
     * @param string $username
     * @return \Illuminate\Http\Response
     */
    public function show($username)
    {
        return User::findByName($username)
            ->withProfileInfo(auth()->user())
            ->first()
            ->append('join_date')
            ->append('permissions');
    }
}
