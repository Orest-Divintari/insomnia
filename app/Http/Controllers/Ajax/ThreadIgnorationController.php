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
        $this->authorize('ignore', $thread);

        auth()->user()->ignore($thread);

        return response('The thread has been marked as ignored successfully', 200);
    }

    /**
     * Mark the thread as unignored
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('unignore', $thread);

        auth()->user()->unignore($thread);

        return response('The thread has been marked as unignored successfully', 200);
    }
}