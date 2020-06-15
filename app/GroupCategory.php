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
     * Load the total number of threads associated with a category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithThreadsCount($query)
    {
        return $query->with(['categories' => function ($query) {
            $threadsCount = DB::table('threads as t1')
                ->select('t1.category_id', DB::raw('count(t1.id) as t1_count'))
                ->groupBy('t1.category_id');

            $threadsCountWithCategory = DB::table('categories as c1')
                ->joinSub($threadsCount, 'threads_count', function ($join) {
                    $join->on('threads_count.category_id', '=', 'c1.id');
                });

            $threadsCountGroupedByCategory = $threadsCountWithCategory
                ->select(DB::raw('(CASE WHEN c1.parent_id is NULL THEN c1.id ELSE c2.id END) as categories_id'), DB::raw(' sum(t1_count) as total_threads_count'))
                ->leftJoin('categories as c2', 'c1.parent_id', '=', 'c2.id')
                ->groupBy(DB::raw('categories_id'));

            return $query->addSelect(['threads_count' => function ($query) use ($threadsCountGroupedByCategory) {
                $query->select('total_threads_count')
                    ->from($threadsCountGroupedByCategory)
                    ->whereColumn('categories.id', 'categories_id');
            },
            ]);
        }]);

    }

    /**
     * Load the total number of replies associated with a category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithRepliesCount($query)
    {
        return $query->with(['categories' => function ($query) {
            $repliesCountPerCategory = DB::table('threads')
                ->select('threads.category_id', DB::raw('count(replies.id) as replies_count'))
                ->join('replies', 'repliable_id', '=', 'threads.id')
                ->groupBy('category_id');

            $repliesCountWithCategoryName = DB::table('categories as category_replies')
                ->joinSub($repliesCountPerCategory, 'replies_count_per_category', function ($join) {
                    $join->on('category_replies.id', '=', 'replies_count_per_category.category_id');
                });

            $repliesCountGroupedByCategory = $repliesCountWithCategoryName
                ->select(
                    DB::raw('sum(replies_count) as category_replies_count'),
                    DB::raw('(CASE WHEN category_replies.parent_id is NULL THEN category_replies.id ELSE parent.id END) as categories_id'))
                ->leftJoin('categories as parent', 'category_replies.parent_id', '=', 'parent.id')
                ->groupBy('categories_id');

            $query->addSelect(['replies_count' => function ($query) use ($repliesCountGroupedByCategory) {
                $query->select('category_replies_count')
                    ->from($repliesCountGroupedByCategory)
                    ->whereColumn('categories_id', 'categories.id');
            }]);

        }]);
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
            $repliesCountPerCategory = DB::table('threads')
                ->select('threads.category_id', DB::raw('count(replies.id) as replies_count'))
                ->join('replies', 'repliable_id', '=', 'threads.id')
                ->groupBy('category_id');

            $repliesCountWithCategoryName = DB::table('categories as category_replies')
                ->joinSub($repliesCountPerCategory, 'replies_count_per_category', function ($join) {
                    $join->on('category_replies.id', '=', 'replies_count_per_category.category_id');
                });

            $repliesCountGroupedByCategory = $repliesCountWithCategoryName
                ->select(
                    DB::raw('sum(replies_count) as category_replies_count'),
                    DB::raw('(CASE WHEN category_replies.parent_id is NULL THEN category_replies.id ELSE parent.id END) as categories_id'))
                ->leftJoin('categories as parent', 'category_replies.parent_id', '=', 'parent.id')
                ->groupBy('categories_id');

            $query->addSelect(['replies_count' => function ($query) use ($repliesCountGroupedByCategory) {
                $query->select('category_replies_count')
                    ->from($repliesCountGroupedByCategory)
                    ->whereColumn('categories_id', 'categories.id');
            }]);

            $threadsCount = DB::table('threads as t1')
                ->select('t1.category_id', DB::raw('count(t1.id) as t1_count'))
                ->groupBy('t1.category_id');

            $threadsCountWithCategory = DB::table('categories as c1')
                ->joinSub($threadsCount, 'threads_count', function ($join) {
                    $join->on('threads_count.category_id', '=', 'c1.id');
                });

            $threadsCountGroupedByCategory = $threadsCountWithCategory
                ->select(DB::raw('(CASE WHEN c1.parent_id is NULL THEN c1.id ELSE c2.id END) as categories_id'), DB::raw(' sum(t1_count) as total_threads_count'))
                ->leftJoin('categories as c2', 'c1.parent_id', '=', 'c2.id')
                ->groupBy(DB::raw('categories_id'));

            return $query->addSelect(['threads_count' => function ($query) use ($threadsCountGroupedByCategory) {
                $query->select('total_threads_count')
                    ->from($threadsCountGroupedByCategory)
                    ->whereColumn('categories.id', 'categories_id');
            },
            ]);
        }]);
    }

}