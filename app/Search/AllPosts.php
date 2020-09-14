<?php

namespace App\Search;

use Algolia\ScoutExtended\Searchable\Aggregator;
use App\ProfilePost;
use App\Reply;
use App\Thread;

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

    protected $relations = [
        ProfilePost::class => ['poster'],
        Thread::class => ['poster', 'category'],
        Reply::class => [
            'poster',
            'repliable.poster',
            'repliable.category',
        ],
    ];
}