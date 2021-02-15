<?php

namespace App;

use App\Traits\FormatsDate;
use Carbon\Carbon;
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
     * Get the user that created the activity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the activities for the user
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function scopeFeed($query, $user)
    {
        return $query->typeCreated()
            ->where('user_id', $user->id)
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
        return $query->typeCreated()
            ->where('user_id', $user->id)
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

    /**
     * Get the activities of type "viewed"
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeTypeViewed($query)
    {
        return $query->where('type', 'like', 'viewed%');
    }

    /**
     * Get the activities of type "created"
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeTypeCreated($query)
    {
        return $query->where('type', 'like', 'created%');
    }

    /**
     * Get the activities of the registered users
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeByMembers($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Get the activities of guests
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeByGuests($query)
    {
        return $query->whereNotNull('guest_id');
    }

    /**
     * Get the activitites that were created the past fifteen minutes
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeLastFifteenMinutes($query)
    {
        return $query->where('created_at', '>=', Carbon::now()->subMinutes(15));
    }

    /**
     * Get the activities that were created the last given minutes
     *
     * @param Builder $query
     * @param int $minutes
     * @return void
     */
    public function scopeLastMinutes($query, int $minutes)
    {
        $query->where('created_at', '>=', Carbon::now()->subMinutes($minutes));
    }

}