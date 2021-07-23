<?php

namespace App\ViewModels;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use App\Models\Thread;

class ThreadsShowViewModel
{
    public function thread($slug, $authUser)
    {
        return Thread::query()
            ->where('slug', $slug)
            ->withIgnoredByVisitor($authUser)
            ->withSubscribed($authUser)
            ->withRecentReply()
            ->with(['poster', 'tags'])
            ->firstOrFail();
    }

    public function replies($thread, $filters, $authUser)
    {
        $replies = $thread->replies()
            ->withCreatorIgnoredByVisitor($authUser)
            ->filter($filters)
            ->withLikes()
            ->paginate(Thread::REPLIES_PER_PAGE);

        foreach ($replies->items() as $reply) {
            $reply->append('permissions');
        }

        return app(AppendHasIgnoredContentAttributeAction::class)
            ->execute($replies);
    }

}
