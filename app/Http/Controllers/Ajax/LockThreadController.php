<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Thread;

class LockThreadController extends Controller
{
    /**
     * Lock the thread
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Thread $thread)
    {
        $this->authorize('lock', $thread);
        $thread->lock();
        return response('The thread has been locked', 200);
    }

    /**
     * Unlock the thread
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('lock', $thread);
        $thread->unlock();
        return response('The thread has been unlocked', 200);
    }
}
