<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Http\Requests\InviteConversationParticipantRequest;
use App\Models\Conversation;

class ConversationParticipantController extends Controller
{

    /**
     * Store a new conversation participant in the database
     *
     * @param Conversation $conversation
     * @param InviteConversationParticipantRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(InviteConversationParticipantRequest $request)
    {
        $request->addParticipants();

        return response('Participants have been added successfully', 200);
    }

    /**
     * Delete the participant from the conversation
     *
     * @param Conversation $conversation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Conversation $conversation, $participantId)
    {
        $this->authorize('manage', $conversation);

        $conversation->removeParticipant($participantId);

        return response('The participant has been removed', 200);
    }
}