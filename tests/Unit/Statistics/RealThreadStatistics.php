<?php

namespace Tests\Unit\Statistics;

use App\Statistics\ThreadStatistics;

class RealThreadStatistics extends ThreadStatistics
{

    public function __construct()
    {

    }

    public function cacheCountKey()
    {
        return 'test_total_theads_count';
    }
}