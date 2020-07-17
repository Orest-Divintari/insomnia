<?php

namespace App;

use App\Thread;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{

    // protected $with = ['threads'];
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

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
     * Determine if it is a root category
     *
     * @return boolean
     */
    public function isRoot()
    {
        return $this->parent_id == null;
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
        return $this->belongsTo(Thread::class);
        // return $this->hasOneThrough(
        //     Thread::class,
        //     Category::class,
        //     'parent_id',
        //     'category_id'
        // )->latest('updated_at')
        //     ->without('poster');
    }

    /**
     * A non-parent category is associated with the most recently active thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recentlyActiveThread()
    {
        return $this->belongsTo(Thread::class);
        // return $this->hasOne(Thread::class)
        //     ->latest('updated_at')
        //     ->without('poster');
    }

    public function scopeRecentActiveThread($query)
    {
        return $query->addSelect([
            'recently_active_thread_id' => Thread::select('id')
                ->whereColumn('category_id', 'categories.id')
                ->latest('updated_at')
                ->take(1),
        ])->with('recentlyActiveThread');

        // $query->addSelect(['recent_reply_id' => DB::table('replies')->select('replies.id')->join('threads', 'replies.repliable_id', '=', 'threads.id')->where('replies.repliable_id', 'threads.id')->latest('created_at')->take(1)]);
        // dd($query->get()->toArray());
        // $query->addSelect([
        //     'recent_reply_id' => Reply::select('id')
        //         ->where('replies.id', 'threads.id')
        //         ->latest('created_at')
        //         ->take(1),
        // ]);

        // dd($query->get()->toArray());
        // $query->withRecentReply();
        // $query->where('threads.id', '=', 50);
        // dd($query->get()->toArray());
        // dd($query->get()->toArray());
        // $query->whereId('threads.id', '=', '5');

    }

    public function scopeParentRecentActiveThread($query)
    {
        $childrenCategoriesIds = DB::table('categories')
            ->select('categories.id')
            ->join('categories as parent_categories', 'categories.parent_id', '=', 'parent_categories.id')
            ->get()
            ->pluck('id');

        return $query->addSelect([
            'parent_category_recently_active_thread_id' => Thread::select('id')
                ->whereIn('category_id', $childrenCategoriesIds)
                ->latest('updated_at')
                ->take(1),
        ])->with('parentCategoryRecentlyActiveThread');
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

    /**
     * Fetch the most recently active thread for the category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithActivity(Builder $query)
    {
        return $query->recentActiveThread();
    }

    /**
     * Fetch the total replies and threads count for the category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithStatistics(Builder $query)
    {
        return $query->withCount([
            'threads',
            'threads as replies_count' => function ($query) {
                $query->select(DB::raw('sum(replies_count)'));
            }]);
    }

    /**
     * Determine if the given category has threads
     *
     * @return boolean
     */
    public function hasThreads()
    {
        return $this->threads->isNotEmpty();
    }

}