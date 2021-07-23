<?php

namespace App\Http\Controllers;

use App\Events\Activity\UserViewedPage;
use App\Filters\ExcludeIgnoredFilter;
use App\Models\User;
use App\ViewModels\ProfileViewModel;

class ProfileController extends Controller
{
    /**
     * Display user's profile
     *
     * @return \Illuminate\View\View
     */
    public function show($username, ExcludeIgnoredFilter $excludeIgnoredFilter)
    {
        $viewModel = new ProfileViewModel($username, auth()->user(), $excludeIgnoredFilter);

        $user = $viewModel->user();

        $this->authorize('view_profile', $user);

        event(new UserViewedPage(UserViewedPage::PROFILE, $user));

        $profilePosts = $viewModel->profilePosts($user);

        return view('profiles.show', compact('user', 'profilePosts'));

    }
}
