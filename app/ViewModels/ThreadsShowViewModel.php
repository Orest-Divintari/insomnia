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
            ->with(['poster', 'tags'])
            ->first();
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