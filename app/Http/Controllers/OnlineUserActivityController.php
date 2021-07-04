<?php

namespace App\Http\Controllers;

use App\ViewModels\OnlineUserActivitiesViewModel;

class OnlineUserActivityController extends Controller
{
    /**
     * Display the activities of the current visitors
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $viewModel = new OnlineUserActivitiesViewModel(request('type'));

        return view('activities.online.index', $viewModel);
    }
}