<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Like;
use App\Reply;

class LikeController extends Controller
{
    /**
     * Store a new like in the database
     *
     * @param Reply $reply
     * @return void
     */
    public function store(Reply $reply)
    {
        $reply->likedBy(auth()->id());

        return response('The post has been liked', 201);
    }

    /**
     * Unlike a reply
     *
     * @param Reply $reply
     * @return void
     */
    public function destroy(Reply $reply)
    {
        $reply->unlikedBy(auth()->id());
        return response('The post has been unliked', 200);
    }
}