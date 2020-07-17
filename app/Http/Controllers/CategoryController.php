<?php

namespace App\Http\Controllers;

use App\Category;
use App\GroupCategory;

class CategoryController extends Controller
{
    /**
     * Display all groups together with the associated categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $groups = GroupCategory::withCategories()->get();
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
        if ($category->hasSubCategories()) {
            $subCategories = $category->subCategories()
                ->withActivity()
                ->withStatistics()
                ->get();
            return view('sub_categories.index', compact('category', 'subCategories'));
        }
        return redirect(route('threads.index', $category->slug));

    }

}