<?php

namespace App\ViewModels;

use App\Thread;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AccountLikesViewModel
{
    /**
     * Get the likes the authenticated user has received
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function receivedLikes()
    {
        return auth()->user()
            ->receivedLikes()
            ->where('liker_id', '!=', auth()->id())
            ->where('type', '!=', 'message-like')
            ->with('liker')
            ->with(['likeable' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Reply::class => ['repliable' => function (MorphTo $morphTo) {
                        $morphTo->morphWith([
                            ProfilePost::class => ['profileOwner'],
                            Thread::class,
                        ]);
                    }],
                    ProfilePost::class => ['profileOwner'],
                ]);
            }])->paginate(20);
    }
}