<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateConversationRequest;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    /**
     * Store a new conversation and send message to participant
     *
     * @param CreateConversationRequest $request
     * @return void
     */
    public function store(Request $request, CreateConversationRequest $conversationRequest)
    {
        $conversation = $conversationRequest->persist();
        $conversation->addParticipants(
            $request->input('participants')
        );
        $conversation->addMessage(
            $request->input('message')
        );

    }

    /**
     * Show the form for creating a new conversation
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('conversations.create');
    }
}