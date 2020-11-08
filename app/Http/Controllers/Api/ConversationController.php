<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    /**
     * Update an existing conversation
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function update(Conversation $conversation)
    {
        $this->authorize('update', $conversation);

        $title = request()->validate([
            'title' => ['required', 'string'],
        ]);
        $conversation->update($title);

        return response('The conversation has been updated', 200);
    }
}