<?php

namespace App\Http\Controllers;

use App\Events\Activity\UserViewedPage;
use App\ProfilePost;
use App\User;

class ProfileController extends Controller
{
    /**
     * Display user's profile
     *
     * @return \Illuminate\View\View
     */
    public function show($username)
    {
        $user = User::withProfileInfo()
            ->whereName($username)
            ->firstOrFail()
            ->append('join_date');

        $this->authorize('view_profile', $user);

        event(new UserViewedPage(UserViewedPage::PROFILE, $user));

        $profilePosts = $user->profilePosts()
            ->withLikes()
            ->latest()
            ->paginate(ProfilePost::PER_PAGE);

        foreach ($profilePosts->items() as $post) {
            $post->append('paginatedComments');
        }

        return view('profiles.show', compact('user', 'profilePosts'));
    }
}