<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Thread;

class ThreadSubscriptionController extends Controller
{
    /**
     * Store a new thread subscription
     *
     * @param Thread $thread
     * @return void
     */
    public function store(Thread $thread)
    {
        $thread->subscribe();
    }

    /**
     * Delete an existing thread subscription
     *
     * @param Thread $thread
     * @return void
     */
    public function destroy(Thread $thread)
    {
        $thread->unsubscribe();
    }
}