<?php
namespace App\Filters;

use App\Filters\FilterInterface;
use App\Filters\Filters;
use App\Models\User;

class ProfilePostFilters extends PostFilters implements FilterInterface
{

    /**
     * Supported filters for threads
     *
     * @var string[]
     */
    public $filters = [
        'postedBy',
        'profileOwner',
        'lastCreated',
        'newPosts',
        'byFollowing',
    ];

    /**
     * Fetch the posts that are on the given user's profile
     *
     * @param String $username
     * @return Builder
     */
    public function profileOwner($username)
    {
        $profileOwner = User::whereName($username)->firstOrFail();

        $this->builder->where('profile_owner_id', '=', $profileOwner->id);
    }

    /**
     * Sort the profile posts by the date that were updated
     *
     * @return Builder
     */
    public function newPosts()
    {
        $this->builder->latest('updated_at');
    }

    /**
     * Fetch the profile posts that are created by the authenticated users
     * or the followings
     *
     * @return Builder
     */
    public function byFollowing()
    {
        $followingUserIds = auth()->user()->followings()->pluck('id');

        $followingUserIds->push(auth()->id());

        $this->builder->whereIn('user_id', $followingUserIds);
    }

}
