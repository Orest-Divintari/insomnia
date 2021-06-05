<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;

class ReadNotificationController extends Controller
{
    /**
     * Mark notification as read
     *
     * @param integer $notificationId
     * @return \Illuminate\Http\Response
     */
    public function update($notificationId)
    {
        auth()->user()
            ->unreadNotifications()
            ->whereId($notificationId)
            ->firstOrFail()
            ->markAsRead();

        return response('The notification has been marked as read', 200);
    }

    /**
     * Mark notification as unread
     *
     * @param integer $notificationId
     * @return \Illuminate\Http\Response
     */
    public function destroy($notificationId)
    {
        auth()->user()
            ->readNotifications()
            ->whereId($notificationId)
            ->firstOrFail()
            ->markAsUnread();

        return response('The notification has been marked as unread', 200);
    }
}