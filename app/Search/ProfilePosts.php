<?php

namespace App\Search;

use Algolia\ScoutExtended\Searchable\Aggregator;
use App\ProfilePost;
use App\Reply;

class ProfilePosts extends Aggregator
{
    /**
     * The names of the models that should be aggregated.
     *
     * @var string[]
     */
    protected $models = [
        'App\ProfilePost',
        'App\Reply',
    ];

    /**
     * Determine the records that are indexed
     *
     * @return boolean
     */
    public function shouldBeSearchable()
    {
        return class_basename($this->model) == 'ProfilePost' || $this->model->repliable_type == 'App\ProfilePost';
    }

    protected $relations = [
        ProfilePost::class => ['poster'],
        Reply::class => ['poster', 'repliable.poster'],
    ];

}