<?php

namespace App\Search;

class ProfilePosts
{
    /**
     * The names of the models that should be aggregated.
     *
     * @var string[]
     */
    protected $models = [
        'App\Models\ProfilePost',
        'App\Models\Reply',
    ];

    /**
     * Determine the records that are indexed
     *
     * @return boolean
     */
    public function shouldBeSearchable()
    {
        return class_basename($this->model) == 'ProfilePost'
        || $this->model->repliable_type == 'App\Models\ProfilePost';
    }

}