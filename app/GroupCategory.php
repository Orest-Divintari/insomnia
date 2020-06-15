<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GroupCategory extends Model
{

    /**
     * Fetch the categories that belong to the group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'group_category_id')
            ->whereNull('parent_id');
    }

    /**
     * Load the the categories and subcategories relationship
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithCategories($query)
    {
        return $query->with([
            'categories.subCategories',
        ]);
    }

    /**
     * Fetch the most recent active thread per category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithActivity($query)
    {
        $query->with([
            'categories.recentlyActiveThread',
            'categories.parentCategoryRecentlyActiveThread',
        ]);
    }

    /**
     * Load the total number of threads and replies associated with a category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithStatistics($query)
    {
        return $query->with(['categories' => function ($query) {

            $query->withCount(['threads', 'parentCategoryThreads']);

            $query->withCount(['threads as replies_count' => function ($query) {
                $query->select(DB::raw('sum(replies_count)'));
            }]);

            $query->withCount(['parentCategoryThreads as parent_category_replies_count' => function ($query) {
                $query->select(DB::raw('sum(replies_count)'));
            }]);
        }]);
    }

}