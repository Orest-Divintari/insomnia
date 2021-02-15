<?php

namespace App\Http\Controllers;

use App\Events\Activity\UserViewedPage;
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

        event(new UserViewedPage(UserViewedPage::PROFILE, $user));

        return view('profiles.show', compact('user'));
    }
}