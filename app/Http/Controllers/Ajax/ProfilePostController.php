<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProfilePostRequest;
use App\Http\Requests\UpdateProfilePostRequest;
use App\ProfilePost;
use App\User;

class ProfilePostController extends Controller
{
    /**
     * Create a new profile post
     *
     * @param User $user
     * @return ProfilePost
     */
    public function store(User $user, CreateProfilePostRequest $request)
    {
        $newPost = auth()->user()->postToProfile(request('body'), $user);
        return $newPost->load('poster');
    }

    /**
     * Update the given profile post
     *
     * @param ProfilePost $post
     * @param UpdateProfilePostRequest $request
     * @return ProfilePost
     */
    public function update(ProfilePost $post, UpdateProfilePostRequest $request)
    {
        return $request->update($post);
    }

    /**
     * Delete the given profile post
     *
     * @param ProfilePost $post
     * @return Http\Response
     */
    public function destroy(ProfilePost $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
    }

    /**
     * Get the posts of the given user's profile
     *
     * @param User $user
     * @return void
     */
    public function index(User $user)
    {
        return $user->profilePosts()
            ->latest()
            ->paginate(ProfilePost::PER_PAGE);
    }
}
