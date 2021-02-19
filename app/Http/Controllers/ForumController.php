<?php

namespace App\Http\Controllers;

use App\Events\Activity\UserViewedPage;
use App\GroupCategory;

class ForumController extends Controller
{
    /**
     * Display all groups together with the associated categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        event(new UserViewedPage(UserViewedPage::FORUM));

        $groups = GroupCategory::withCategories()->get();

        return view('categories.index', compact('groups'));
    }
}