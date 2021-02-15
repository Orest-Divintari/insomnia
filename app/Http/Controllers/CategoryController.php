<?php

namespace App\Http\Controllers;

use App\Actions\ActivityLogger;
use App\Category;
use App\Events\Activity\UserViewedPage;
use App\GroupCategory;

class CategoryController extends Controller
{
    protected $logger;

    public function __construct(ActivityLogger $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Display all groups together with the associated categories
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        event(new UserViewedPage(UserViewedPage::FORUM));

        $groups = GroupCategory::withCategories()->get();
        if (request()->expectsJson()) {
            return $groups;
        }

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

        event(new UserViewedPage(UserViewedPage::CATEGORY, $category));

        if ($category->hasSubCategories()) {
            $subCategories = $category->subCategories()
                ->withThreadsCount()
                ->withRepliesCount()
                ->withRecentlyActiveThread()
                ->get();

            return view('sub_categories.index', compact('category', 'subCategories'));
        }
        return redirect(route('threads.index', $category->slug));
    }

}