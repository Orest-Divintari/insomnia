<?php

namespace Tests\Unit\Statistics;

use App\Statistics\ThreadReplyStatistics;

class RealThreadReplyStatistics extends ThreadReplyStatistics
{

    public function __construct()
    {

    }

    public function cacheCountKey()
    {
        return 'test_total_thead_replies_count';
    }
}