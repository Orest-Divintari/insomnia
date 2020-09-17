<?php
namespace App\Filters;

use App\Filters\Filters;
use App\Filters\PostsFilter;
use App\User;

class ProfilePostFilters extends PostsFilter
{

    /**
     * Supported filters for threads
     *
     * @var array
     */
    protected $filters = [
        'postedBy',
        'profileOwner',
        'lastCreated',
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

}