<?php

namespace App\Http\Controllers;

use App\Thread;

class RecentlyViewedThreadsController extends Controller
{
    /**
     * Fetch the most recently viewed threads
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $threads = Thread::recentlyViewedBy(auth()->user())
            ->with('poster')
            ->with('category')
            ->withReadAt()
            ->withHasBeenUpdated()
            ->withRecentReply()
            ->paginate(50);

        return view('threads.recently_viewed', compact('threads'));
    }
}