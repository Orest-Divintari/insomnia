<?php

namespace App\Http\Middleware;

use App\Exceptions\PostThrottlingException;
use Carbon\Carbon;
use Closure;

class ThrottlePosts
{
    /**
     * The time frame in seconds
     * a user has to wait before creating a new post
     *
     * @var integer
     */
    protected $timeFrame = 30;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // return $next($request);
        $lastPostActivity = auth()->user()->lastPostActivity();

        if ($lastPostActivity && $this->tooSoonToPost($lastPostActivity)) {

            throw new PostThrottlingException(
                $this->secondsLeftBeforePosting($lastPostActivity)
            );
        }

        return $next($request);
    }

    /**
     * Determine whether the user's last post was created within the accepted timeframe
     *
     * @param Activity $post
     * @return bool
     */
    public function tooSoonToPost($post)
    {
        return $post->created_at > Carbon::now()->subSeconds($this->timeFrame);
    }

    /**
     * Get the seconds remaining to perform post action
     *
     * @param Activity $post
     * @return int
     */
    public function secondsLeftBeforePosting($post)
    {
        return $post->created_at
            ->diffInSeconds(Carbon::now()->subSeconds($this->timeFrame));
    }

    public function getTimeFrame()
    {
        return $this->timeFrame;
    }

}