<?php

namespace App\Http\Controllers;

use App\Models\Reply;

class CommentController extends Controller
{
    /**
     * Redirect to the profile post that comment belongs to
     *
     * @param Reply $comment
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Reply $comment)
    {
        return redirect($comment->path);
    }
}
