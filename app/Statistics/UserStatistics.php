<?php

namespace App\Statistics;

class UserStatistics
{
    use HandlesCache;

    private $cacheCountKey = 'total_users_count';
}