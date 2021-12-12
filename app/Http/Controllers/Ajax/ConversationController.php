<?php

namespace App\Http\Controllers\Ajax;

use App\Filters\ExcludeIgnoredFilter;
use App\Filters\FilterManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateConversationRequest;
use App\Models\Conversation;

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

        return $conversation->append('permissions');
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
            ->withHasBeenUpdated()
            ->excludeIgnored(auth()->user(), $excludeIgnoredFilter)
            ->filter($conversationFilters)
            ->with('starter')
            ->with('participants')
            ->orderByUnread()
            ->orderByUpdated()
            ->get();
    }
}