<?php

namespace App\ViewModels;

use App\Conversation;

class ConversationsShowViewModel
{

    protected $conversation;
    protected $appendHasIgnoredContentAttributeAction;

    public function __construct($conversation, $appendHasIgnoredContentAttributeAction)
    {
        $this->conversation = $conversation;
        $this->appendHasIgnoredContentAttributeAction = $appendHasIgnoredContentAttributeAction;
    }

    public function conversation()
    {
        return Conversation::query()
            ->where('slug', $this->conversation->slug)
            ->withRecentMessage()
            ->withCount(['messages', 'participants'])
            ->withHasBeenUpdated()
            ->withIsStarred()
            ->firstOrFail();
    }

    public function messages()
    {
        $messages = $this->conversation
            ->messages()
            ->withIgnoredByVisitor(auth()->user())
            ->withLikes()
            ->paginate(Conversation::REPLIES_PER_PAGE);

        return $this->appendHasIgnoredContentAttributeAction->execute($messages);
    }

    public function participants()
    {
        return $this->conversation
            ->participants()
            ->withConversationAdmin($this->conversation)
            ->get();
    }
}