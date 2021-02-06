<?php

namespace App;

use App\Traits\FormatsDate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
    use FormatsDate;

    /**
     * Number of activities per page
     *
     * @var int
     */
    const NUMBER_OF_ACTIVITIES = 10;

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the associated subject for the activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get the activities for the user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function scopeFeed($query, $user)
    {
        return $query->where('user_id', '=', $user->id)
            ->latest()
            ->with(['subject' => function ($morphTo) {
                $morphTo->morphWith([
                    Thread::class => ['poster', 'category'],
                    Reply::class => ['repliable' => function (MorphTo $morphTo) {
                        $morphTo->morphWith([
                            Thread::class => ['category'],
                            ProfilePost::class => ['profileOwner'],
                        ]);
                    }],
                    Like::class => ['reply.repliable'],
                    ProfilePost::class => ['profileOwner'],
                ]);
            }]);
    }

    /**
     * Get the activity of all posts
     *
     * @param  User $user
     * @return Builder
     */
    public function scopeFeedPosts($query, $user)
    {
        return $query->where('user_id', $user->id)
            ->whereIn('subject_type', [
                'App\Thread',
                'App\Reply',
                'App\ProfilePost',
            ])->latest()
            ->with(['subject' => function ($morphTo) {
                $morphTo->morphWith([
                    Thread::class => ['poster', 'category', 'tags'],
                    Reply::class => ['repliable' => function (MorphTo $morphTo) {
                        $morphTo->morphWith([
                            Thread::class => ['category'],
                            ProfilePost::class => ['profileOwner'],
                        ]);
                    }],
                    ProfilePost::class => ['profileOwner'],
                ]);
            }]);
    }
}