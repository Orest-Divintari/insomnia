<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Thread;

class PinThreadController extends Controller
{
    /**
     * Mark thread as pinned
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Thread $thread)
    {
        $this->authorize('pin', $thread);
        $thread->pin();
        return response('Pinned thread', 200);
    }

    /**
     * Mark thread as unpinned
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('pin', $thread);
        $thread->unpin();
        return response('Unpinned thread', 200);
    }
}
