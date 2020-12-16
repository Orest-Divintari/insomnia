<?php

namespace App\Search;

use App\Search\SearchIndexInterface;
use App\Tag;
use App\Thread;

class SearchTags implements SearchIndexInterface
{
    /**
     * Search tags
     *
     * @param array $searchQuery
     * @return Builder
     */
    public function search($searchQuery)
    {
        $tagIds = Tag::whereIn('name', [$searchQuery])->pluck('id');

        return Thread::whereHas('tags', function ($query) use ($tagIds) {
            $query->whereIn('tags.id', $tagIds);
        })->withSearchInfo();
    }
}