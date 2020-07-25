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
    const PER_PAGE = 3;

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
        ])->load('poster')
            ->loadCount('likes')
            ->append('is_liked');
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
        $this->authorize('deleteComment', $comment);
        $comment->delete();
        return response('Comment has been deleted', 200);
    }

    /**
     * Get the comments associated with the post
     *
     * @param ProfilePost $post
     * @return void
     */
    public function index(ProfilePost $post)
    {
        $comments = $post->comments()->withCount('likes')->latest()->paginate(static::PER_PAGE);
        $comments->each(function ($comment) {
            $comment->append('is_liked');
        });
        return $comments;
    }

}