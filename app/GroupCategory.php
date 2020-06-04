<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupCategory extends Model
{

    /**
     * Returns the categories that belong to the group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->hasMany(Category::class, 'group_category_id');
    }

    /**
     * Fetch the parent categories of the group
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function withCategories()
    {
        return static::with(['categories' => function ($query) {
            $query->whereNull('parent_id');
        }])->get();
    }

}