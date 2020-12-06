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
    public function update(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        auth()->user()->read($conversation);
        return response('Conversation has been marked as read', 200);
    }
}