<?php

namespace App\Http\Controllers;

use App\Repositories\OnlineRepository;

class OnlineUserActivityController extends Controller
{
    /**
     * Get the activities of the current visitors
     *
     * @param OnlineRepository $online
     * @return \Illuminate\View\View
     */
    public function index(OnlineRepository $online)
    {
        $type = request('type');
        return view('activities.online.index', [
            'activities' => $online->activities($type),
            'membersCount' => $online->membersCount(),
            'guestsCount' => $online->guestsCount(),
            'totalUsersCount' => $online->totalUsersCount(),
            'type' => $type,
        ]);
    }
}