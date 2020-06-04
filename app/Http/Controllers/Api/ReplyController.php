<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Reply as ReplyResource;
use App\Thread;

class ReplyController extends Controller
{

    public function index(Thread $thread)
    {
        return ReplyResource::collection($thread->replies);

    }

    public function show()
    {
        return response(200);
    }
}