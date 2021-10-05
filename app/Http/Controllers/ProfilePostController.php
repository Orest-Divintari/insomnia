<?php

namespace App\Http\Controllers;

use App\Filters\ExcludeIgnoredFilter;
use App\Filters\FilterManager;
use App\Models\ProfilePost;
use App\ViewModels\ProfilePostsViewModel;

class ProfilePostController extends Controller
{
    public function show(ProfilePost $post)
    {
        return redirect($post->reversePath);
    }

    /**
     * Display a listing of profile psots
     *
     * @param ExcludeIgnoredFilter $excludeIgnoredFilter
     * @param FilterManager $filterManager
     * @return \Illuminate\View\View
     */
    public function index(ExcludeIgnoredFilter $excludeIgnoredFilter, FilterManager $filterManager)
    {
        $profilePostFilter = $filterManager->withProfilePostFilters();

        $viewModel = new ProfilePostsViewModel(
            auth()->user(),
            $excludeIgnoredFilter,
            $profilePostFilter
        );

        return view('profile-posts.index')
            ->with([
                'profilePosts' => $viewModel->profilePosts(),
                'profilePostFilters' => $profilePostFilter->getRequestedFilters(),
            ]);
    }
}