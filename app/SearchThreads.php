<?php

use App\Thread;

namespace App;

class SearchThreads
{
    public function query()
    {
        return Thread::search(request('q'));
    }
}