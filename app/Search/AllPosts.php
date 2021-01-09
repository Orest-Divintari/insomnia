<?php

namespace App\Search;

use Algolia\ScoutExtended\Searchable\Aggregator;

class AllPosts extends Aggregator
{
    /**
     * The names of the models that should be aggregated.
     *
     * @var string[]
     */
    protected $models = [
        'App\Thread',
        'App\Reply',
        'App\ProfilePost',
    ];

    public function shouldBeSearchable()
    {
        // The first thread-reply consists the body of the thread
        // therefore it should not be searchable
        if ($this->model->isThreadReply()) {
            return !$this->model->isThreadBody();
        }

        if ($this->model->isMessage()) {
            return false;
        }

        return true;
    }

}