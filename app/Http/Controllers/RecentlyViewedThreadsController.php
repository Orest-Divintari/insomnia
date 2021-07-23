<?php

namespace App\Http\Controllers;

use App\Models\Thread;

class RecentlyViewedThreadsController extends Controller
{
    /**
     * Display the most recently viewed threads
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

        return view('threads.recently-viewed', compact('threads'));
    }
}
