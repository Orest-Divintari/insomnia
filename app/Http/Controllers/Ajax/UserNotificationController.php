<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserNotificationController extends Controller
{

    /**
     * Fetch the user notifications since last week
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->user()->viewNotifications();

        return auth()->user()
            ->fresh()
            ->notificationsSinceLastWeek()
            ->get();
    }
}
