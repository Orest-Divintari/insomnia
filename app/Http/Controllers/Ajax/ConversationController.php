<?php

namespace App\Http\Controllers\Ajax;

use App\Conversation;
use App\Filters\ExcludeIgnoredFilter;
use App\Filters\FilterManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConversationRequest;

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
        $request->update($conversation);

        return response('The conversation has been updated', 200);
    }

    /**
     * Get the conversations for the authenticated user
     *
     * @param FilterManager $filterManager
     * @param ExcludeIgnoredFilter $excludeIgnoredFilter
     * @return \Illuminate\Http\Response
     */
    public function index(FilterManager $filterManager, ExcludeIgnoredFilter $excludeIgnoredFilter)
    {
        $conversationFilters = $filterManager->withConversationFilters();

        return auth()->user()->conversations()
            ->excludeIgnored(auth()->user(), $excludeIgnoredFilter)
            ->filter($conversationFilters)
            ->withHasBeenUpdated()
            ->with('starter')
            ->with('participants')
            ->orderByUnread()
            ->orderByUpdated()
            ->get();
    }
}