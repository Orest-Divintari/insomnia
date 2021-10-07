<?php

namespace Tests\Unit\Statistics;

use App\Statistics\ThreadStatistics;

class FakeThreadStatistics extends ThreadStatistics
{

    public function __construct()
    {
        $this->count = 0;
    }

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function count()
    {
        return $this->count;
    }

    public function resetCount()
    {
        $this->count = 0;
    }
}