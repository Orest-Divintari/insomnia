<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Thread;

class ReplyController extends Controller
{

    const PER_PAGE = 1;

    public function index(Thread $thread)
    {
        return $thread->replies()->paginate(static::PER_PAGE);

    }

    public function show()
    {
        return response(200);
    }
}