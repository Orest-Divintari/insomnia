<?php

namespace App\Http\Controllers;

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
            ->withLikesCount()
            ->paginate(2);

        return view('account.follows.index', compact('followingUsers'));
    }
}