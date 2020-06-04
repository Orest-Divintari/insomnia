<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Thread as ThreadResource;
use App\Thread;

class ThreadController extends Controller
{
    //

    /**
     * Returns a thread resource
     *
     * @param \App\Thread $thread
     * @return \App\Http\Resources\Thread
     */
    public function show(Thread $thread)
    {
        return new ThreadResource($thread);
    }

    /**
     * Create a new thread
     *
     * @return void
     */
    public function store()
    {

    }

}