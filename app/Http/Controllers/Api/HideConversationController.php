<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;

class HideConversationController extends Controller
{
    /**
     * Hide the conversation from the authenticated user
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function store(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $conversation->hideFrom(auth()->user());
        return response('The conversation has been hidden successfully', 200);
    }
}