<?php

namespace App\Http\Controllers\Ajax;

use App\Conversation;
use App\Http\Controllers\Controller;

class ConversationAdminController extends Controller
{
    /**
     * Set participant as admin
     *
     * @param Conversation $conversation
     * @return void
     */
    public function update(Conversation $conversation, $participantId)
    {
        $this->authorize('manage', $conversation);
        $conversation->setAdmin($participantId);
        return response('The member has been set as admin', 200);
    }

    /**
     * Rmove participant from admin
     *
     * @param Conversation $conversation
     * @return void
     */
    public function destroy(Conversation $conversation, $participantId)
    {
        $this->authorize('manage', $conversation);
        $conversation->removeAdmin($participantId);
        return response('The member has been removed as admin', 200);
    }
}