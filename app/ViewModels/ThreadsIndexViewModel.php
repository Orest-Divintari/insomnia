<?php

namespace App\ViewModels;

use App\Filters\ExcludeIgnoredFilter;
use App\Models\Category;
use App\Models\Thread;

class ThreadsIndexViewModel
{

    protected $excludeIgnoredFilter;
    protected $category;

    public function __construct(Category $category, ExcludeIgnoredFilter $excludeIgnoredFilter, $threadFilters)
    {
        $this->category = $category;
        $this->threadFilters = $threadFilters;
        $this->excludeIgnoredFilter = $excludeIgnoredFilter;
    }

    public function threadQuery()
    {
        return Thread::query()
            ->excludeIgnored(auth()->user(), $this->excludeIgnoredFilter)
            ->with('poster')
            ->withHasBeenUpdated()
            ->withRecentReply()
            ->forCategory($this->category)
            ->latest('updated_at')
            ->filter($this->threadFilters);
    }

    public function threads()
    {
        return $this->threadQuery()
            ->paginate(Thread::PER_PAGE);
    }

    public function pinnedThreads()
    {
        return $this->threadQuery()
            ->pinned()
            ->get();
    }

}