<?php

namespace App\Http\Controllers;

class AccountNotificationsController extends Controller
{
    /**
     * Display a listing of notifications
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('account.notifications.index', [
            'user' => auth()->user(),
            'notifications' => auth()->user()->unreadNotifications()->paginate(50),
        ]);
    }
}