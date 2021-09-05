<?php

namespace App\Search;

use App\Models\ProfilePost;
use App\Models\Reply;
use App\Models\Tag;
use App\Models\Thread;

class ModelsResolver
{
    private $modelClasses = [
        'threads' => Thread::class,
        'profile_posts' => ProfilePost::class,
        'replies' => Reply::class,
        'tags' => Tag::class,
    ];

    public function fromIndexName($indexName)
    {
        return app($this->modelClasses[$indexName]);
    }
}