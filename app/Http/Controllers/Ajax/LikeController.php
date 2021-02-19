<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Like;
use App\Reply;

class LikeController extends Controller
{
    /**
     * Store a new like in the database
     *
     * @param Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function store(Reply $reply)
    {
        $reply->likedBy(auth()->user());

        return response('The post has been liked', 204);

    }

    /**
     * Unlike a the given reply
     *
     * @param Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $reply->unlikedBy(auth()->user());
        return response('The post has been unliked', 204);
    }
}
