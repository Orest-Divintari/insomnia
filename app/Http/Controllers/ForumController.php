<?php

namespace App\Http\Controllers;

use App\Events\Activity\UserViewedPage;
use App\ViewModels\ForumViewModel;

class ForumController extends Controller
{
    /**
     * Display all groups together with the associated categories
     *
     * @return \Illuminate\View\View
     */
    public function index(ForumViewModel $viewModel)
    {
        event(new UserViewedPage(UserViewedPage::FORUM));

        return view('forum.index')->with([
            'groups' => $viewModel->groups(),
            'latestPosts' => $viewModel->latestPosts(),
            'statistics' => $viewModel->statistics(),
        ]);
    }
}