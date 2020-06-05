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
        $groups = $this->group->withParentCategories();
        return view('categories.index', compact('groups'));
    }

    /**
     * Display subcategory if exsits or redirect to associated threads
     *
     * @param Category $category
     * @return mixed
     */
    public function show(Category $category)
    {
        if ($category->children()->exists()) {
            return view('categories.sub_categories.index', [
                'subCategories' => $category->children,
            ]);
        }
        return redirect(route('threads.index', $category->slug));

    }

}