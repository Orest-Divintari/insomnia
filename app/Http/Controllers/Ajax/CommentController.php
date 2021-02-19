<?php

namespace App\Http\Controllers\Ajax;

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
        return $post->addComment(
            request('body'),
            auth()->user()
        )->load('poster')
            ->loadCount('likes');
    }

    /**
     * Update an existing comment
     *
     * @param Reply $comment
     * @param UpdateCommentRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(Reply $comment, UpdateCommentRequest $request)
    {
        $request->update($comment);
        return response()->noContent();
    }

    /**
     * Delete an existing comment
     *
     * @param Reply $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response('Comment has been deleted', 200);
    }

    /**
     * Get the comments associated with the profile post
     *
     * @param ProfilePost $post
     * @return array
     */
    public function index(ProfilePost $post)
    {
        return Reply::forProfilePost($post);
    }

}