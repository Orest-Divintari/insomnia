<?php

namespace App\Http\Controllers;

use App\Category;
use App\Events\Activity\UserViewedPage;

class CategoryController extends Controller
{
    /**
     * Display sub category if exists or redirect to associated threads
     *
     * @param Category $category
     * @return mixed
     */
    public function show(Category $category)
    {

        event(new UserViewedPage(UserViewedPage::CATEGORY, $category));

        if ($category->hasSubCategories()) {
            $subCategories = $category->subCategories()
                ->withThreadsCount()
                ->withRepliesCount()
                ->withRecentlyActiveThread()
                ->get();

            return view('categories.show', compact('category', 'subCategories'));
        }
        return redirect(route('category-threads.index', $category->slug));
    }

}