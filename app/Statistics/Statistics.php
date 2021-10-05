<?php

namespace App\Statistics;

class Statistics
{
    public function threads()
    {
        return app(ThreadStatistics::class);
    }

    public function threadReplies()
    {
        return app(ThreadReplyStatistics::class);
    }

    public function users()
    {
        return app(UserStatistics::class);
    }
}