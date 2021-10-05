<?php

namespace App\Statistics;

class ThreadReplyStatistics
{
    use HandlesCache;

    private $cacheCountKey = "total_thread_replies_count";
}