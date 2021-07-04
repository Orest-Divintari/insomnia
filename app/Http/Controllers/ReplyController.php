<?php

namespace App\Http\Controllers;

use App\Reply;

class ReplyController extends Controller
{
    /**
     * Display a specific reply
     *
     * Find the page the given reply belongs to
     * And go directly to that specific reply in the page
     *
     * @param Reply $reply
     * @return void
     */
    public function show(Reply $reply)
    {
        return redirect($reply->path);
    }
}