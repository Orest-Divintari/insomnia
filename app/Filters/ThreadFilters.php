<?php

namespace App\Filters;

use App\Filters\Filters;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ThreadFilters extends Filters
{

    /**
     * Supported filters for threads
     *
     * @var array
     */
    protected $filters = [
        'startedBy',
        'newThreads',
        'newPosts',
        'contributed',
        'trending',
        'unanswered',
        'watched',
        'lastUpdated',
        'lastCreated',
    ];

    /**
     * Fetch the threads for the given username
     *
     * @param String $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function startedBy($username)
    {
        $user = User::whereName($username)->firstOrFail();

        $this->builder->where('user_id', $user->id);

    }

    /**
     * Fetch the most recently created threads
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newThreads()
    {

        $this->builder->orderBy('created_at', 'DESC');

    }

    /**
     * Fetch the threads with the most recent replies
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function newPosts()
    {
        $this->builder->where('replies_count', '>', 0)
            ->orderBy('updated_at', 'DESC');
    }

    /**
     * Fetch the threads that you have participated
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function constributed($username)
    {
        $user = User::where('name', $username)->firstOrFail();

        $this->builder->whereHas("replies", function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });

    }

    /**
     * Get the Trending threads
     *
     * The trending thread is defined by the number of replies and views
     *
     * @return \Illuminate\Database\Eloquent\Builder
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function unanswered()
    {
        $this->builder->where('replies_count', '=', '0');
    }

    /**
     * Get the threads that the authenticated user has subscribed to
     *
     * @return \Illuminate\Database\Eloquent\Builder
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function lastUpdated($numberOfDays)
    {
        $this->builder
            ->where('updated_at', ">=", Carbon::now()->subDays($numberOfDays));
    }

    /**
     * Get the threads that were created the last give number of days
     *
     * @param int $numberOfDays
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function lastCreated($numberOfDays)
    {
        $this->builder
            ->where('created_at', ">=", Carbon::now()->subDays($numberOfDays));
    }

    /**
     * Get the filter keys and values passed in the request
     *
     * @return array
     */
    public function getThreadFilters()
    {
        $filters = $this->findFilters();

        $filters = $this->castValues($filters);

        $this->validateFilters($filters->toArray());
        return $filters;
    }

    /**
     * Find the the supported filters
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findFilters()
    {
        return collect($this->request->all())
            ->filter(function ($value, $key) {
                return in_array($key, $this->filters);
            });
    }

    /**
     * Cast the requested filter values to boolean
     *
     * @param \Illuminate\Database\Eloquent\Collection $filters
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function castValues($filters)
    {
        return $filters->map(function ($value, $key) {

            if ($value == 'true') {
                $value = true;
            } elseif ($value == 'false') {
                $value = false;
            }
            return $value;
        });
    }

    /**
     * Validate the requested filters
     *
     * @param array $filters
     * @return void
     */
    public function validateFilters($filters)
    {
        return Validator::make($filters, [
            'startedBy' => "sometimes|required|string|exists:users,name",
            'contributed' => "sometimes|required|string|exists:users,name",
            'contributed' => "sometimes|required|string|exists:users,name",
            'lastUpdated' => 'sometimes|required|integer|min:0',
            'lastCreated' => 'sometimes|required|integer|min:0',
            'newThreads' => 'sometimes|required|boolean',
            'newPosts' => 'sometimes|required|boolean',
            'watched' => 'sometimes|required|boolean',
            'unanswered' => 'sometimes|required|boolean',
            'trending' => 'sometimes|required|boolean',
        ]);
    }

}