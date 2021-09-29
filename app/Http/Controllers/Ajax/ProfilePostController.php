<?php

namespace App\Http\Controllers\Ajax;

use App\Events\Profile\ProfilePostWasUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProfilePostRequest;
use App\Http\Requests\UpdateProfilePostRequest;
use App\Models\ProfilePost;
use App\Models\User;

class ProfilePostController extends Controller
{
    /**
     * Create a new profile post
     *
     * @param User $user
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function update(ProfilePost $post, UpdateProfilePostRequest $request)
    {
        
        $post = $request->update($post);

        event(new ProfilePostWasUpdated($post, auth()->user()));

        return response($post, 200);
    }

    /**
     * Delete the given profile post
     *
     * @param ProfilePost $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfilePost $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response('The post has been deleted', 200);
    }
}