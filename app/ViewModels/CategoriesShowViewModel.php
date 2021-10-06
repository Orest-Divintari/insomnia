<?php

namespace App\ViewModels;

use Illuminate\Support\Facades\Cache;

class CategoriesShowViewModel
{
    /**
     * Get the sub categories for the given category
     *
     * @param Category $category
     * @return Collection
     */
    public function subCategories($category)
    {
        return Cache::remember(
            "categories.{$category->id}.show",
            60,
            function ()
             use ($category) {
                return $category->subCategories()
                    ->withThreadsCount()
                    ->withRepliesCount()
                    ->withRecentlyActiveThread()
                    ->get();
            });
    }
}