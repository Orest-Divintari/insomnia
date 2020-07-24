<?php

namespace App\Http\Controllers\Api;

use App\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\ProfilePost;
use App\Reply;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a new comment
     *
     * @param ProfilePost $post
     * @param PostCommentRequest $request
     * @return ProfilePost
     */
    public function store(ProfilePost $post, PostCommentRequest $request)
    {
        return $post->addComment([
            'user_id' => auth()->id(),
            'body' => request('body'),
        ]);
    }

    /**
     * Update an existing comment
     *
     * @param Reply $comment
     * @param UpdateCommentRequest $request
     * @return Http\Response
     */
    public function update(Reply $comment, UpdateCommentRequest $request)
    {
        $request->update($comment);
        return response()->noContent();
    }

    public function destroy(Reply $comment)
    {
        $this->authorize('deleteComment', $comment);
        $comment->delete();
    }
}