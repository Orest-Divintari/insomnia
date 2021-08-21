<?php

namespace App\Search;

use App\Models\Reply;

class AllPosts
{
    /**
     * The names of the models that should be aggregated.
     *
     * @var string[]
     */
    protected $models = [
        'App\Models\Thread',
        'App\Models\Reply',
        'App\Models\ProfilePost',
    ];

    public function shouldBeSearchable()
    {
        // The first thread-reply consists the body of the thread
        // therefore it should not be searchable
        if ($this->isReply($this->model)) {
            if ($this->model->isThreadReply()) {
                return !$this->model->isThreadBody();
            }
            if ($this->model->isMessage()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine whether the model is reply
     *
     * @param mixed $model
     * @return boolean
     */
    public function isReply($model)
    {
        return get_class($model) == Reply::class;
    }

}