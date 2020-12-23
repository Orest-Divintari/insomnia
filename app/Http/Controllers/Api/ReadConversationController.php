<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;

class ReadConversationController extends Controller
{
    /**
     * Mark conversation as read
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function store(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        auth()->user()->read($conversation);
        return response('Conversation has been marked as read', 200);
    }

    /**
     * Mark conversation as unread
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        auth()->user()->unread($conversation);
        return response('Conversation has been marked as unread', 200);
    }
}