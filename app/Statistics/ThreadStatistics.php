<?php

namespace App\Statistics;

class ThreadStatistics
{
    use HandlesCache;

    private $cacheCountKey = "total_threads_count";
}