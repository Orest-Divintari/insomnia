<?php

namespace Tests\Unit\Statistics;

use App\Statistics\ThreadStatistics;

class FakeThreadStatistics extends ThreadStatistics
{

    public function __construct()
    {

    }

    public function cacheCountKey()
    {
        return 'test_total_theads_count';
    }
}