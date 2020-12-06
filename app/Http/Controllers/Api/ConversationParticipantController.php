<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Http\Controllers\Controller;
use App\Http\Requests\InviteConversationParticipant;

class ConversationParticipantController extends Controller
{

    /**
     * Store a new conversation participant in the database
     *
     * @param Conversation $conversation
     * @param InviteConversationParticipant $request
     * @return \Illuminate\Http\Response
     */
    public function store(InviteConversationParticipant $request)
    {
        $request->conversation
            ->addParticipants(request('participants'));
        return response('Participants have been added successfully', 200);
    }
}