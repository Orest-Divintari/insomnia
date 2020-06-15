<?php

namespace App\Http\Controllers;

use App\Category;
use App\GroupCategory;

class CategoryController extends Controller
{

    /**
     *
     *
     * @param GroupCategory $group
     */
    public function __construct(GroupCategory $group)
    {
        $this->group = $group;
    }

    /**
     * Display all groups together with the associated categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $groups = GroupCategory::withCategories()->withStatistics()->get();
        return view('categories.index', compact('groups'));
    }

    /**
     * Display sub category if exists or redirect to associated threads
     *
     * @param Category $category
     * @return mixed
     */
    public function show(Category $category)
    {
        if ($category->subCategories->isNotEmpty()) {

            $subCategories = $category->subCategories;
            return view('sub_categories.index', compact('subCategories'));
        }
        return redirect(route('threads.index', $category->slug));

    }

}