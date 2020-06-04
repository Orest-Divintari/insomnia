<?php

namespace App\Http\Controllers;

use App\Category;

class CategoryController extends Controller
{

    /**
     * Display all categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::where('parent_id', null)->get();
        return view('categories.index', compact('categories'));
    }

    /** Display subcategory if it exsits  */
    public function show(Category $category)
    {
        if ($category->children()->exists()) {

            return view('categories.sub_categories.index', ['subCategories' => $category->children]);
        }
        return redirect(route('threads.index', $category->slug));

    }

}