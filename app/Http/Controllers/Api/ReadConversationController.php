<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;

class ReadConversationController extends Controller
{
    /**
     * Store a new read record for the conversation
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function store(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        auth()->user()->readConversation($conversation);

        return response('Conversation has been marked as read', 200);
    }

    /**
     * 
     *
     * @param Conversation $conversation
     * @return void
     */
    public function destroy(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        auth()->user()->unreadConversation($conversation);

        return response('Conversation has been marked as unread', 200);
    }
}