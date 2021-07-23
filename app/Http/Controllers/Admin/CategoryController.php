<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\GroupCategory;

class CategoryController extends Controller
{
    /**
     * Display the form for creating a new category
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.categories.create', [
            'allCategories' => Category::all(),
            'allGroupCategories' => GroupCategory::all(),
        ]);
    }

    /**
     * Store a new category
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateCategoryRequest $request)
    {
        $request->persist();
    }

    /**
     * Display the form for updating the given category
     *
     * @param Category $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category)
    {
        $allCategories = Category::except($category)->get();
        $allGroupCategories = GroupCategory::except($category->group)->get();

        return view(
            'admin.categories.edit',
            compact('category', 'allCategories', 'allGroupCategories')
        );
    }

    /**
     * Update the given category
     *
     * @param Category $category
     * @param UpdateCategoryRequest $request
     * @return \Illuminate\HTTP\RedirectResponse
     */
    public function update(Category $category, UpdateCategoryRequest $request)
    {
        $request->update($category);

        return redirect(route('admin.categories.index'));
    }

    /**
     * Display the categories list
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::query()
            ->with('category')
            ->with('group')
            ->withThreadsCount()
            ->withRepliesCount()
            ->withDescendantCategoriesCount()
            ->paginate(Category::PER_PAGE);

        return view('admin.categories.index', compact('categories'));
    }
}
