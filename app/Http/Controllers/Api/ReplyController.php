<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Thread;

class ReplyController extends Controller
{

    public function index(Thread $thread)
    {
        return $thread->replies()->paginate(config('constants.reply.per_page'));

    }

    public function show()
    {
        return response(200);
    }
}