<?php

namespace App;

use App\Filters\ThreadFilters;
use App\Search\Threads;

class SearchThreads
{

    protected $filters;

    public function __cosntruct()
    {
        dd('sssss');
    }

    public function query()
    {
        $filters = app(ThreadFilters::class);

        if (request('only_title') == true) {
            return Thread::search(request('q'));
        }

        return $filters->apply(Threads::search(request('q')));

    }
}