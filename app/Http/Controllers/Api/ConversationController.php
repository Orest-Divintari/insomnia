<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConversationRequest;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    /**
     * Update an existing conversation
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Conversation $conversation, UpdateConversationRequest $request)
    {
        $conversation->update(['title' => request('title')]);
        return response('The conversation has been updated', 200);
    }
}