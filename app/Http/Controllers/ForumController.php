<?php

namespace App\Http\Controllers;

use App\Events\Activity\UserViewedPage;
use App\GroupCategory;
use Illuminate\Http\Request;

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
        if (request()->expectsJson()) {
            return $groups;
        }

        return view('categories.index', compact('groups'));
    }
}