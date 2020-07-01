<?php

namespace App\Http\Controllers\Api;

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