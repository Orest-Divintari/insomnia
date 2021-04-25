<?php

namespace App\Http\Controllers;

use App\Reply;

class CommentController extends Controller
{
    public function show(Reply $comment)
    {
        return redirect($comment->url);
    }
}