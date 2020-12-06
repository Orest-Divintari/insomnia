<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;

class UnreadConversationController extends Controller
{
    /**
     * Mark conversation as unread
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        auth()->user()->unread($conversation);
        return response('Conversation has been marked as unread', 200);
    }
}