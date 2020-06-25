<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Reply;
use App\Thread;

class ReplyController extends Controller
{

    /**
     * Display paginated list of replies
     *
     * @param Thread $thread
     * @return \Illuminate\Http\Response
     */
    public function index(Thread $thread)
    {
        return $thread->replies()->paginate(Reply::PER_PAGE);

    }

}