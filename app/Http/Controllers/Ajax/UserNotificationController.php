<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\User;

class UserNotificationController extends Controller
{

    /**
     * Fetch the unread notifications for the user
     *
     * @return array
     */
    public function index()
    {
        auth()->user()->viewNotifications();
        return auth()->user()->fresh()->unreadNotifications;
    }

    /**
     * Mark a notification as read
     *
     * @param int $notificationId
     * @return void
     */
    public function destroy($notificationId)
    {
        auth()->user()
            ->unreadNotifications()
            ->findOrFail($notificationId)
            ->markAsRead();
    }
}