<?php

namespace App;

use App\Thread;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    /**
     * The relationships to always eager-load.
     *
     * @var array
     */
    protected $with = ['children'];
    /**
     * The relationships count to always eager-load.
     *
     * @var array
     */
    protected $withCount = ['threads'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the string path of a category
     *
     * @return void
     */
    public function path()
    {
        return '/forum/categories/' . $this->slug;
    }

    /**
     * A category has a parent category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * A category belongs to a group
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function group()
    {
        return $this->belongsTo(GroupCategory::class, 'group_category_id');
    }

    /**
     * A category has sub-categories
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * A category has threads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * Determines the path to category's avatar
     *
     * @param string $avatar
     * @return string
     */
    public function getAvatarPathAttribute($avatar)
    {
        return asset($avatar ?: '/avatars/categories/apple_logo.png');
    }
}