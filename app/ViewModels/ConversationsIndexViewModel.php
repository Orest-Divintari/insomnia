<?php

namespace App\ViewModels;

use App\Models\Conversation;

class ConversationsIndexViewModel
{
    public function conversations($conversationFilters, $excludeIgnoredFilter)
    {
        return auth()->user()
            ->conversations()
            ->excludeIgnored(auth()->user(), $excludeIgnoredFilter)
            ->filter($conversationFilters)
            ->withHasBeenUpdated()
            ->withIsStarred()
            ->withRecentMessage()
            ->with(['starter', 'participants'])
            ->withCount(['messages', 'participants'])
            ->latest('conversations.updated_at')
            ->paginate(Conversation::PER_PAGE);

    }
}
