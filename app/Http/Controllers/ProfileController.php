<?php

namespace App\Http\Controllers;

use App\User;

class ProfileController extends Controller
{
    /**
     * Display user's profile
     *
     * @return void
     */
    public function show($username)
    {
        $user = User::withProfileInfo()
            ->whereName($username)
            ->first()
            ->append('join_date');

        return view('profiles.show', compact('user'));
    }
}