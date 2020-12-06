<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Thread;

class ReadThreadController extends Controller
{
    /**
     * Mark a thread as read
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Thread $thread)
    {
        auth()->user()->read($thread);
        return response('The thread has been marked as read susccessfully', 200);
    }
}