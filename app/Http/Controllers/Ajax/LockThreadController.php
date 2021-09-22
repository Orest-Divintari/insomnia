<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Thread;

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

        return $thread->append('permissions');
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

        return $thread->append('permissions');
    }
}