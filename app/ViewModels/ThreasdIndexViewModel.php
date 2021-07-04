<?php

namespace App\ViewModels;

use App\Category;
use App\Filters\ExcludeIgnoredFilter;
use App\Thread;

class ThreasdIndexViewModel
{

    protected $excludeIgnoredFilter;
    protected $category;

    public function __construct(Category $category, ExcludeIgnoredFilter $excludeIgnoredFilter, $threadFilters)
    {
        $this->category = $category;
        $this->threadFilters = $threadFilters;
        $this->excludeIgnoredFilter = $excludeIgnoredFilter;
    }

    public function threads()
    {
        return Thread::query()
            ->excludeIgnored(auth()->user(), $this->excludeIgnoredFilter)
            ->with('poster')
            ->withHasBeenUpdated()
            ->withRecentReply()
            ->forCategory($this->category)
            ->filter($this->threadFilters)
            ->latest('updated_at')
            ->paginate(Thread::PER_PAGE);
    }

    public function pinnedThreads()
    {
        return Thread::query()
            ->excludeIgnored(auth()->user(), $this->excludeIgnoredFilter)
            ->with('poster')
            ->withHasBeenUpdated()
            ->withRecentReply()
            ->forCategory($this->category)
            ->filter($this->threadFilters)
            ->latest('updated_at')
            ->pinned()
            ->get();
    }

}