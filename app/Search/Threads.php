<?php

namespace App\Search;

use Algolia\ScoutExtended\Searchable\Aggregator;
use App\Reply;
use App\Thread;

class Threads extends Aggregator
{
    /**
     * The names of the models that should be aggregated.
     *
     * @var string[]
     */
    protected $models = [
        'App\Thread',
        'App\Reply',
    ];

    /**
     * Determine the records that are indexed
     *
     * @return boolean
     */
    public function shouldBeSearchable()
    {
        return class_basename($this->model) == 'Thread' || ($this->model->repliable_type == 'App\Thread' && $this->model->position > 1);
    }

    protected $relations = [
        Thread::class => ['poster', 'category'],
        Reply::class => [
            'poster',
            'repliable.category',
            'repliable.poster',
        ],
    ];

}