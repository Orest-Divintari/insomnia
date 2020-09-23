<?php

namespace App\Search;

use App\Thread;
use Illuminate\Support\Facades\DB;

class SearchThreadTitles
{

    /**
     * Search threads
     *
     * @return mixed
     */
    public function query()
    {
        $filters = app('ThreadFilters');
        $q = request('q');

        if (isset($q)) {
            $query = Thread::search($q);
        } else {
            $query = DB::table('activities');
        }

        return $filters->apply($query);

    }

}