<?php

namespace App;

use App\Thread;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Relationships to always eager-load
     *
     * @var array
     */
    protected $with = [
        'recentlyActiveThread',
        'parentCategoryRecentlyActiveThread',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the string path of a category
     *
     * @return string
     */
    public function path()
    {
        return '/forum/categories/' . $this->slug;
    }

    /**
     * A subCategory belongs to a category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'parent_id');
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
     * A parent category has sub categories
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Determine if a category has sub-categories
     *
     * @return boolean
     */
    public function hasSubCategories()
    {
        return $this->subCategories->isNotEmpty();
    }

    /**
     * A non-parent category has threads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    /**
     * A parent category has threads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parentThreads()
    {
        return $this->hasManyThrough(
            Thread::class,
            Category::class,
            'parent_id',
            'category_id'
        );
    }

    /**
     * A parent category is associated with sub-category's threads
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function parentCategoryThreads()
    {
        return $this->hasManyThrough(
            Thread::class,
            Category::class,
            'parent_id',
            'category_id'
        );
    }

    /**
     * A parent category is associated with the most recently active thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function parentCategoryRecentlyActiveThread()
    {
        return $this->hasOneThrough(
            Thread::class,
            Category::class,
            'parent_id',
            'category_id'
        )->latest('updated_at');
    }

    /**
     * A non-parent category is associated with the most recently active thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recentlyActiveThread()
    {
        return $this->hasOne(Thread::class)->latest('updated_at');
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