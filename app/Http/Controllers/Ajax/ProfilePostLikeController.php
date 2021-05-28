<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\ProfilePost;

class ProfilePostLikeController extends Controller
{
    /**
     * Store a new like in the database for the given profile post
     *
     * @param ProfilePost $reply
     * @return \Illuminate\Http\Response
     */
    public function store(ProfilePost $profilePost)
    {
        $profilePost->likedBy(auth()->user());

        return response('The post has been liked', 200);

    }

    /**
     * Delete the like of the given profile post
     *
     * @param ProfilePost $profilePost
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfilePost $profilePost)
    {
        $profilePost->unlikedBy(auth()->user());

        return response('The post has been unliked', 200);
    }
}