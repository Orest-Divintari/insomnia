<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Thread;

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
        $thread->read();

        return response('The thread has been marked as read susccessfully', 200);
    }
}
