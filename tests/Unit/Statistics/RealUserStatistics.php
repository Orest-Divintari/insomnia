<?php

namespace Tests\Unit\Statistics;

use App\Statistics\UserStatistics;

class RealUserStatistics extends UserStatistics
{

    public function __construct()
    {

    }

    public function cacheCountKey()
    {
        return 'test_total_users_count';
    }
}