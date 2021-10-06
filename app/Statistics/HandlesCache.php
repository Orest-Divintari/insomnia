<?php

namespace App\Statistics;

use Illuminate\Support\Facades\Cache;

trait HandlesCache
{
    public function cacheCountKey()
    {
        return $this->cacheCountKey;
    }

    public function count()
    {
        return Cache::get($this->cacheCountKey()) ?? 0;
    }

    public function increment()
    {
        return Cache::increment($this->cacheCountKey());
    }

    public function decrement()
    {
        return Cache::decrement($this->cacheCountKey());
    }

    public function resetCount()
    {
        return Cache::forget($this->cacheCountKey());
    }

}