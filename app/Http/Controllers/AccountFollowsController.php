<?php

namespace App\Http\Controllers;

use App\Follow;

class AccountFollowsController extends Controller
{
    /**
     * Display a listing of following users
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $followingUsers = auth()->user()
            ->follows()
            ->withMessagesCount()
            ->withCount('receivedLikes')
            ->paginate(Follow::FOLLOWS_PER_PAGE);

        return view('account.follows.index', compact('followingUsers'));
    }
}