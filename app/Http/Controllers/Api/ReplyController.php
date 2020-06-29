<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostReplyRequest;
use App\Http\Requests\UpdateReplyRequest;
use App\Reply;
use App\Thread;
use Illuminate\Support\Facades\Http;

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
        return Reply::withCount('likes')
            ->where('repliable_id', $thread->id)
            ->paginate(Reply::PER_PAGE);
    }

    /**
     * Store a newly created reply in storage
     *
     * @param Thread $thread
     * @param PostReplyRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread, PostReplyRequest $request)
    {
        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id(),
        ])->load('poster')
            ->loadCount('likes');
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
        $this->authorize('manage', $reply);
        $reply->delete();
        return response('Reply has been deleted', 200);
    }

}