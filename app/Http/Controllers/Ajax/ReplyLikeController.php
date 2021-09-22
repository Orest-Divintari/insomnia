<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Reply;

class ReplyLikeController extends Controller
{
    /**
     * Store a new like in the database
     *
     * @param Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function store(Reply $reply)
    {
        $this->authorize('like', $reply);

        $reply->like(auth()->user());

        return response('The post has been liked', 200);

    }

    /**
     * Unlike a the given reply
     *
     * @param Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $reply->unlike(auth()->user());

        return response('The post has been unliked', 200);
    }
}