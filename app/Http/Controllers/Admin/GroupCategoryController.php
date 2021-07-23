<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateGroupCategoryRequest;
use App\Http\Requests\UpdateGroupCategoryRequest;
use App\Models\GroupCategory;

class GroupCategoryController extends Controller
{

    /**
     * Display the form to create a new group category
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.group-categories.create');
    }

    /**
     * Store a new group category
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateGroupCategoryRequest $request)
    {
        $request->persist();

        return redirect(route('admin.group-categories.index'));
    }

    /**
     * Update the given group category
     *
     * @param GroupCategory $groupCategory
     * @param UpdateGroupCategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GroupCategory $groupCategory, UpdateGroupCategoryRequest $request)
    {
        $request->update($groupCategory);

        return back();
    }

    /**
     * Display a listing of the group categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $groupCategories = GroupCategory::query()
            ->withCount('categories')
            ->withThreadsCount()
            ->withRepliesCount()
            ->paginate(GroupCategory::PER_PAGE);

        return view('admin.group-categories.index', compact('groupCategories'));
    }

    /**
     * Display the form to edit the given group category
     *
     * @param GroupCategory $groupCategory
     * @return \Illuminate\View\View
     */
    public function edit(GroupCategory $groupCategory)
    {
        return view('admin.group-categories.edit', compact('groupCategory'));
    }
}
