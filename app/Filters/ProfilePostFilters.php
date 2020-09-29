<?php
namespace App\Filters;

use App\Filters\Filters;
use App\Filters\PostFilters;
use App\User;

class ProfilePostFilters extends PostFilters
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

    public $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

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