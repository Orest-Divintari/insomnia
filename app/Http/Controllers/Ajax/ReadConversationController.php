<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Conversation;

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

        $conversation->read();

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

        $conversation->unread();

        return response('Conversation has been marked as unread', 200);
    }
}
