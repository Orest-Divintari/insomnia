<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
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
     * Eager-Load the categories and subcategories relationship
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeWithCategories(Builder $query)
    {

        return $query->with([
            'categories.subCategories',
            'categories' => function ($query) {

                $query->withRecentActiveThread();

                $query->withParentRecentActiveThread();

                $query->withCount(['threads', 'parentCategoryThreads']);

                $query->withCount(['threads as replies_count' => function ($query) {
                    $query->select(DB::raw('sum(replies_count)'));
                }]);

                $query->withCount(['parentCategoryThreads as parent_category_replies_count' => function ($query) {
                    $query->select(DB::raw('sum(replies_count)'));
                }]);
            },
        ]);
    }

    /**
     * Fetch the most recent active thread per category
     *
     * If it is a parent category, then fetch
     * the most recently active thread among all its children
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeWithActivity(Builder $query)
    {
        $query->with([
            'categories.recentlyActiveThread',
            'categories.parentCategoryRecentlyActiveThread',
        ]);
    }

    /**
     * Eager-Load the total number of threads and replies associated with a category
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeWithStatistics(Builder $query)
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