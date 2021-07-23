<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Conversation;

class HideConversationController extends Controller
{
    /**
     * Hide the conversation from the authenticated user
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $conversation->hideFrom(auth()->user());

        return response('The conversation has been hidden successfully', 200);
    }
}
