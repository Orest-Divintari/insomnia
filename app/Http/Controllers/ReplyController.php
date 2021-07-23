<?php

namespace App\Http\Controllers;

use App\Models\Reply;

class ReplyController extends Controller
{
    /**
     * Display a specific reply
     *
     * Find the page the given reply belongs to
     * And go directly to that specific reply in the page
     *
     * @param Reply $reply
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Reply $reply)
    {
        return redirect($reply->path);
    }
}
