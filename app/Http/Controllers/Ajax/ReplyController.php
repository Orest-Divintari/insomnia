<?php

namespace App\Http\Controllers\Ajax;

use App\Events\Thread\ThreadReplyWasUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\PostReplyRequest;
use App\Http\Requests\UpdateReplyRequest;
use App\Models\Reply;
use App\Models\Thread;
use Illuminate\Support\Facades\Http;

class ReplyController extends Controller
{

    /**
     * Store a newly created reply in storage
     *
     * @param Thread $thread
     * @param PostReplyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread, PostReplyRequest $request)
    {
        $reply = $thread->addReply($request->validated(), auth()->user())
            ->load('poster')
            ->loadCount('likes')
            ->append('permissions');

        return response($reply, 201);
    }

    /**
     * Update an existing reply
     *
     * @param Reply $reply
     * @param UpdateReplyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(Reply $reply, UpdateReplyRequest $request)
    {
        $request->update($reply);

        event(new ThreadReplyWasUpdated($reply->repliable, $reply, auth()->user()));

        return response('Reply has been updated', 200);
    }

    /**
     * Delete an existing reply
     *
     * @param Reply $reply
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reply $reply)
    {
        $this->authorize('delete', $reply);

        $reply->delete();

        return response('Reply has been deleted', 200);
    }
}