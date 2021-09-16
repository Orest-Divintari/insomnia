<?php

namespace App\Filters;

use App\Filters\FilterInterface;
use App\Filters\Filters;
use App\Models\User;

class ElasticProfilePostFilters extends ElasticPostFilters implements FilterInterface
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
     * @return Builder
     */
    public function profileOwner($username)
    {
        $profileOwnerId = User::whereName($username)->first()->id;

        $this->builder->filter('term', ['profile_owner_id' => $profileOwnerId]);
    }

}