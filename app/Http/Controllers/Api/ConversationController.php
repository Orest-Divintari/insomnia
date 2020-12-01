<?php

namespace App\Http\Controllers\Api;

use App\Conversation;
use App\Filters\FilterManager;
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

    /**
     * Get the conversations for the authenticated user
     *
     * @return \Illuminate\Http\Response
     */
    public function index(FilterManager $filterManager)
    {
        $conversationFilters = $filterManager->withConversationFilters();
        return Conversation::filter($conversationFilters)
            ->with('starter')
            ->with('participants:name')
            ->get()
            ->sortByDesc('has_been_updated')
            ->values();

    }
}