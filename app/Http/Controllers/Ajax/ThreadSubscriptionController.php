<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Thread;
use App\Models\User;

class ThreadSubscriptionController extends Controller
{
    /**
     * Store or update thread subscription
     *
     * It is possible to enable or disable email notifications
     * for a thread subscription
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function update(Thread $thread)
    {
        $this->authorize('subscribe', $thread);

        request()->validate([
            'email_notifications' => 'required|boolean',
        ]);

        $this->subscribe($thread, auth()->id());

        return response('You have subsribed successfully', 200);

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

        return response('You have unsubsribed successfully', 200);
    }

    /**
     * Update thread subscription for the given thread and user
     *
     * @param Thread $thread
     * @param int $userId
     * @return void
     */
    private function subscribe($thread, $userId)
    {
        if (request()->boolean('email_notifications')) {
            $thread->subscribe($userId);
            return;
        }
        $thread->subscribeWithoutEmailNotifications($userId);
    }
}
