<?php

namespace App;

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
        return $this->hasMany(Category::class, 'group_category_id');
    }

    /**
     * Fetch the parent categories that belong to the group
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parentCategories()
    {
        return $this->hasMany(Category::class, 'group_category_id')->whereNull('parent_id');
    }

}