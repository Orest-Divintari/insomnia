<?php

namespace App\Models;

use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCategory extends Model
{

    use Sluggable, HasFactory;

    /**
     * Number of group categories to display per page
     */
    const PER_PAGE = 20;

    /**
     * Mass assignable attributes
     *
     * @var array
     */
    // protected $fillable = ['title', 'excerpt'];

    protected $guarded = [];

    /**
     * Get the route key name
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

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
                return $query
                    ->withThreadsCount()
                    ->withRepliesCount()
                    ->withRecentlyActiveThread();
            },
        ]);
    }

    public function scopeWithCategoriesCount($query)
    {
        if (is_null($query->getQuery()->columns)) {
            $query->addSelect('*');
        }

        return $query->selectRAW('(
            select
                count(*)
            from
                categories
            where
                categories.group_category_id = group_categories.id
        )');
    }

    /**
     * Add a column with the threads count for each group category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithThreadsCount($query)
    {
        if (is_null($query->getQuery()->columns)) {
            $query->addSelect('*');
        }

        return $query->selectRAW('(
            select
               count(*)
            from
               threads
            where
               threads.category_id in
               (
                  select
                     categories.id
                  from
                     categories
                  where
                     categories.group_category_id = group_categories.id
               )
            ) as threads_count');
    }

    /**
     * Add a column with the replies count for each group category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithRepliesCount($query)
    {
        if (is_null($query->getQuery()->columns)) {
            $query->addSelect('*');
        }

        return $query->selectRaw('(
            select
               count(*)
            from
               replies
            where
               replies.repliable_type =?
               and replies.position IS NOT NULL
               and replies.position > 1
               and replies.repliable_id in
               (
                  select
                     id
                  from
                     threads
                  where
                     threads.category_id in
                     (
                        select
                           categories.id
                        from
                           categories
                        where
                           categories.group_category_id = group_categories.id
                     )
               )
            ) as replies_count', [Thread::class]);
    }

    /**
     * Exclude the given group
     *
     * @param Builder $query
     * @param GroupCategory $groupCategory
     * @return Builder
     */
    public function scopeExcept($query, $groupCategory)
    {
        return $query->where('id', '!=', $groupCategory->id);
    }
}