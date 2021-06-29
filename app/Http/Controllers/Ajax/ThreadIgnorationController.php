<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Thread;

class ThreadIgnorationController extends Controller
{

    /**
     * Mark the thread as ignored
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread)
    {
        $thread->markAsIgnored();

        return response('The thread has been marked as ignored successfully', 200);
    }

    /**
     * Mark the thread as unignored
     *
     * @param string $threadSlug
     * @return \Illuminate\Http\Response
     */
    public function destroy($threadSlug)
    {
        $thread = Thread::where('slug', $threadSlug)->first();

        $thread->markAsUnignored();

        return response('The thread has been marked as unignored successfully', 200);
    }
}