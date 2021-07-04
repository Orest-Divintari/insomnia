<?php

namespace App\Http\Controllers\Ajax;

use App\Conversation;
use App\Http\Controllers\Controller;

class LeaveConversationController extends Controller
{
    /**
     * Authenticated user leaves the conversation
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->leftBy(auth()->user());

        return response('The conversation has been left successfully', 200);
    }
}