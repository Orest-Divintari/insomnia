<?php

namespace App\Http\Controllers\Ajax;

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
    public function update(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->starredBy();

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

        $conversation->unstarredBy();

        return response('Conversation unstarred', 200);
    }
}