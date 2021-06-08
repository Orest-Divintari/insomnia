<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;

class ReadAllNotificationsController extends Controller
{
    /**
     * Mark all unread notifications as read
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        auth()->user()
            ->unreadNotifications()
            ->markAllAsRead();

        return response('All unread notifications have been marked as read', 200);
    }
}