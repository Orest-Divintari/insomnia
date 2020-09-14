<?php

namespace App;

use App\Search\Threads;

class SearchThreads
{

    public function query()
    {
        if (request('only_title') == true) {
            return Thread::search(request('q'));
        }

        return Threads::search(request('q'));

    }
}