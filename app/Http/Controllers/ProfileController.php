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
    public function show(User $user)
    {
        $user->append('messages_count', 'likes_score', 'join_date', 'followed_by_visitor');

        if (request()->expectsJson()) {
            return $user;
        }

        return view('profiles.show', compact('user'));
    }
}