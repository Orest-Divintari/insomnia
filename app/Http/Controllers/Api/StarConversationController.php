<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;

class StarConversationController extends Controller
{
    /**
     * Mark the conversation as starred for the authenticated user
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function store(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $conversation->star();
        return response('Conversation starred', 200);
    }

    /**
     * Mark the conversation as unstarred for the authenticated user
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $conversation->unstar();
        return response('Conversation unstarred', 200);
    }
}