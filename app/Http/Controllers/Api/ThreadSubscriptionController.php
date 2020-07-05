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
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread)
    {
        request()->validate([
            'email_notifications' => 'required|boolean',
        ]);

        $thread->subscribe(auth()->id(), request('email_notifications'));

        return response()->noContent();

    }

    /**
     * Delete an existing thread subscription
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $thread->unsubscribe();

        return response()->noContent();
    }
}