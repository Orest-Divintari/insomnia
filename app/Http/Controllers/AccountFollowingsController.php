<?php

namespace App\Http\Controllers;

use App\Models\Follow;

class AccountFollowingsController extends Controller
{
    /**
     * Display a listing of following users
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $followingUsers = auth()->user()
            ->followings()
            ->withMessagesCount()
            ->withCount('receivedLikes')
            ->paginate(Follow::FOLLOWINGS_PER_PAGE);

        return view('account.followings.index', compact('followingUsers'));
    }
}
