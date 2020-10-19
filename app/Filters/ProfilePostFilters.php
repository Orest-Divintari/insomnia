<?php
namespace App\Filters;

use App\Filters\Filters;
use App\User;
use FilterInterface;

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
    ];

    /**
     * Fetch the posts that are on the given user's profile
     *
     * @param String $username
     * @return void
     */
    public function profileOwner($username)
    {
        $profileOwner = User::whereName($username)->firstOrFail();

        $this->builder->where('profile_owner_id', '=', $profileOwner->id);
    }

}