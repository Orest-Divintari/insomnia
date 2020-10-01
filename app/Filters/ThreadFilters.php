<?php

namespace App\Filters;

use App\Filters\Filters;
use App\Thread;
use App\User;
use Carbon\Carbon;

class ThreadFilters extends Filters
{

    /**
     * Supported filters for threads
     *
     * @var array
     */
    public $filters = [
        'postedBy',
        'newThreads',
        'newPosts',
        'contributed',
        'trending',
        'unanswered',
        'watched',
        'lastUpdated',
        'lastCreated',
        'numberOfReplies',
    ];

    /**
     * Fetch the most recently created threads
     *
     * @return void
     */
    public function newThreads()
    {
        $this->builder->orderBy('created_at', 'DESC');
    }

    /**
     * Fetch the threads with the most recent replies
     *
     * @return void
     */
    public function newPosts()
    {
        $this->builder->addSelect([
            'recent_reply_created_date' => Reply::select('created_at')
                ->whereColumn('repliable_id', 'threads.id')
                ->where('repliable_type', 'App\Thread')
                ->latest('created_at')
                ->take(1),
        ])->orderBy('recent_reply_created_date', 'DESC');
    }

    /**
     * Fetch the threads that you have participated
     *
     * @return void
     */
    public function contributed($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        $this->builder->whereHas('replies', function ($query) use ($user) {
            $query->where([
                ['position', ">", 1],
                'user_id' => $user->id,
            ]);
        });
    }

    /**
     * Get the Trending threads
     *
     * The trending thread is defined by the number of replies and views
     *
     * @return void
     */
    public function trending()
    {
        $this->builder->where('replies_count', '>', 0)
            ->orderBy('replies_count', 'DESC')
            ->orderBy('views', 'DESC');
    }

    /**
     * Get the threads that have no replies
     *
     * @return void
     */
    public function unanswered()
    {
        $this->builder->where('replies_count', '=', '0');
    }

    /**
     * Get the threads that the authenticated user has subscribed to
     *
     * @return void
     */
    public function watched()
    {
        $this->builder->whereHas('subscriptions', function ($query) {
            $query->where('user_id', auth()->id());
        });

    }

    /**
     * Get the threads that were last updated before the given number of days
     *
     * @param int $numberOfDays
     * @return void
     */
    public function lastUpdated($numberOfDays)
    {

        $this->builder
            ->where('updated_at', ">=", Carbon::now()->subDays($numberOfDays));
    }

    // /**
    //  * Get the threads with minimum number of rerplies
    //  *
    //  * @param int $numberOfReplies
    //  * @return void
    //  */
    // public function numberOfReplies($numberOfReplies)
    // {
    //     $this->builder->where('replies_count', '>=', $numberOfReplies);
    // }

}