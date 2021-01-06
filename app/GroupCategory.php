<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
                return $query
                    ->withThreadsCount()
                    ->withRepliesCount()
                    ->withRecentlyActiveThread();
            },
        ]);
    }
}