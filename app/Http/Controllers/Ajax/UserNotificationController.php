<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\User;

class UserNotificationController extends Controller
{

    /**
     * Fetch the unread notifications for the user
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->user()->viewNotifications();

        return auth()->user()
            ->fresh()
            ->notifications()
            ->sinceLastWeek()
            ->get();
    }
}