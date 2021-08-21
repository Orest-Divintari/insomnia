<?php

namespace App\Search;

use App\Models\Thread;

class Threads
{
    /**
     * The names of the models that should be aggregated.
     *
     * @var string[]
     */
    protected $models = [
        Thread::class,
        'App\Models\Reply',
    ];

    /**
     * Determine the records that are indexed
     *
     * @return boolean
     */
    public function shouldBeSearchable()
    {
        return class_basename($this->model) == 'Thread' || ($this->model->repliable_type == 'App\Models\Thread' && $this->model->position > 1);
    }

}