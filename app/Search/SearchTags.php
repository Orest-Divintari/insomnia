<?php

namespace App\Search;

use App\Models\Tag;
use App\Models\Thread;
use App\Search\SearchIndexInterface;

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
