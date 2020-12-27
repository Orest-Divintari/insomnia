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

        return response($reply->fresh(), 201);
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
        $this->authorize('delete', $reply);
        $reply->delete();
        return response('Reply has been deleted', 200);
    }

    /**
     * Display a specific reply
     *
     * Find the page the given reply belongs to
     * And go directly to that specific reply in the page
     *
     * @param Reply $reply
     * @return void
     */
    public function show(Reply $reply)
    {
        return redirect(
            route('threads.show', $reply->repliable) .
            "?page=" . $reply->pageNumber .
            '#post-' . $reply->id
        );
    }
}