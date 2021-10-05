<?php

namespace App\Statistics;

use Illuminate\Support\Facades\Redis;

trait HandlesCache
{
    public function cacheCountKey()
    {
        return $this->cacheCountKey;
    }

    public function count()
    {
        return Redis::get($this->cacheCountKey()) ?? 0;
    }

    public function increment()
    {
        return Redis::incr($this->cacheCountKey());
    }

    public function decrement()
    {
        return Redis::decr($this->cacheCountKey());
    }

    public function resetCount()
    {
        return Redis::del($this->cacheCountKey());
    }

}