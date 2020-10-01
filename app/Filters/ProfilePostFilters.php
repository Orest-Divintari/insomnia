<?php
namespace App\Filters;

use App\Filters\Filters;
use App\User;

class ProfilePostFilters extends Filters
{

    /**
     * Supported filters for threads
     *
     * @var array
     */
    public $filters = [
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