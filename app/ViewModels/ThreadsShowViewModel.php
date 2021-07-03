<?php

namespace App\ViewModels;

use App\Actions\AppendHasIgnoredContentAttributeAction;
use App\Thread;

class ThreadsShowViewModel
{
    public function thread($slug, $authUser)
    {
        return Thread::query()
            ->where('slug', $slug)
            ->withIgnoredByVisitor($authUser)
            ->withSubscribed($authUser)
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

        return app(AppendHasIgnoredContentAttributeAction::class)
            ->execute($replies);
    }

}