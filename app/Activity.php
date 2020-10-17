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
                            ProfilePost::class => ['profileOwner'],
                        ]);
                    }],
                    Like::class => ['reply.repliable'],
                    ProfilePost::class => ['profileOwner'],
                ]);
            }]);
    }

    /**
     * Fetch only the posting activities
     *
     * @param  Builder
     * @return Builder
     */
    public function scopeOfAllPosts($query)
    {
        return $query->where('subject_type', '!=', 'App\Like');
    }

    /**
     * Get the activity of profile posts and comments
     * and eager load the required relationships for displaying as search result
     *
     * @param Builder $query
     * @return array
     */
    public function scopeOfProfilePostsAndComments($query)
    {
        $commentsActivity = $query->whereHasMorph('subject', ['App\Reply'], function ($builder) {
            $builder->onlyComments();
        })->with(['subject' => function ($builder) {
            $builder->withSearchInfo();
        }])->addSelect([
            'profile_owner_id' => ProfilePost::select('profile_owner_id')
                ->whereRaw('profile_posts.id =
                (
                   SELECT
                      repliable_id
                   from
                      replies
                   where
                      replies.id = activities.subject_id
                )'),
        ]);

        $profilePostsActivity = Activity::where(
            'subject_type', '=', 'App\ProfilePost'
        )->with(['subject' => function ($builder) {
            $builder->withSearchInfo();
        }])->addSelect([
            'profile_owner_id' => ProfilePost::select('profile_owner_id')
                ->whereColumn('profile_posts.id', 'activities.subject_id'),
        ]);
        return [$commentsActivity, $profilePostsActivity];
    }

    /**
     * Get the activity for threads and replies and eager load the respective relationships
     * for displaying as a search result
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOfThreadsAndReplies($query)
    {
        $repliesActivity = $query
            ->whereHasMorph('subject', ['App\Reply'], function ($builder) {
                $builder->onlyReplies();
            })->with(['subject' => function ($builder) {
            $builder->withSearchInfo();
        }]);

        $threadsActivity = Activity::where('subject_type', 'App\Thread')
            ->with(['subject' => function ($builder) {
                $builder->withSearchInfo();
            }]);

        // $repliesActivity = $query->whereHasMorph('subject', ['App\Reply'], function ($builder) {
        //     $builder->onlyReplies();
        // })->addSelect(
        //     [
        //         'replies_count' => Thread::select('replies_count')
        //             ->whereRaw('threads.id=(SELECT repliable_id from replies where replies.id=activities.subject_id)'),
        //     ]
        // )->with(['subject' => function ($builder) {
        //     $builder->withSearchInfo();
        // }]);

        // $threadsActivity = Activity::where('subject_type', 'App\Thread')
        //     ->addSelect(
        //         ['replies_count' => Thread::select('replies_count')
        //                 ->whereColumn('threads.id', 'activities.subject_id'),
        //         ]
        //     )->with(['subject' => function ($builder) {
        //     $builder->withSearchInfo();
        // }]);
        return [$repliesActivity, $threadsActivity];
    }
}