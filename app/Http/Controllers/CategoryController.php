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
     * Display all groups together with the associated parent categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        $groups = GroupCategory::with(['categories.RecentlyActiveThread', 'categories.children'])->get();

        return view('categories.parent.index', compact('groups'));
    }

    /**
     * Display sub category if exsits or redirect to associated threads
     *
     * @param Category $category
     * @return mixed
     */
    public function show(Category $category)
    {
        if ($category->subCategories->isNotEmpty()) {

            $subCategories = $category->subCategories;
            return view('categories.children.index', compact('subCategories'));
        }
        return redirect(route('threads.index', $category->slug));

    }

}