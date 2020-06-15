<?php

namespace App\Http\Controllers;

use App\GroupCategory;

class GroupCategoryController extends Controller
{

    /**
     * Display groups and associated categories
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groups = GroupCategory::withCategories()->withStatistics()->get();
        return view('categories.group.index', compact('groups'));
    }
}