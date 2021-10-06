<?php

namespace App\Http\Controllers;

use App\Events\Activity\UserViewedPage;
use App\Models\Category;
use App\ViewModels\CategoriesShowViewModel;

class CategoryController extends Controller
{
    /**
     * Display sub category if exists or redirect to associated threads
     *
     * @param Category $category
     * @return mixed
     */
    public function show(Category $category, CategoriesShowViewModel $viewModel)
    {
        event(new UserViewedPage(UserViewedPage::CATEGORY, $category));

        if ($category->hasSubCategories()) {
            $subCategories = $viewModel->subCategories($category);
            return view('categories.show', compact('category', 'subCategories'));
        }
        return redirect(route('category-threads.index', $category->slug));
    }

}