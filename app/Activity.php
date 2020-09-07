<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Activity extends Model
{
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
    public static function feed($user)
    {
        return static::where('user_id', '=', $user->id)
            ->latest()
            ->with(['subject' => function ($morphTo) {
                $morphTo->morphWith([
                    Thread::class => ['poster', 'category'],
                    Reply::class => ['repliable' => function (MorphTo $morphTo) {
                        $morphTo->morphWith([
                            Thread::class => ['category'],
                        ]);
                    }],
                    Like::class => ['reply.repliable'],
                ]);
            }]);
    }

    /**
     * Fetch only the posting activities
     *
     * @return Builder
     */
    public function scopeOnlyPostings($query)
    {
        return $query->where('subject_type', '!=', 'App\Like');
    }
}