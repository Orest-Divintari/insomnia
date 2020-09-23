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
        if (($this->model->repliable_type == 'App\Thread')) {
            return $this->model->position > 1;
        }
        return true;
    }

}