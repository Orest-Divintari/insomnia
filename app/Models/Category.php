<?php

namespace App\Models;

use App\Models\Thread;
use App\Traits\Sluggable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    /**
     * Numbe of categories per page
     */
    const PER_PAGE = 10;

    use Sluggable, HasFactory;

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
     * A sub-category belongs to a category
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
     * A parent category has sub-categories
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function descendantCategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->with('descendantCategories');
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
     * Get the category's tree
     *
     * @return Collection
     */
    public function tree()
    {
        $query = Category::whereId($this->id)
            ->unionAll(
                Category::select('categories.*')
                    ->join('tree', 'tree.id', '=', 'categories.parent_id')
            );

        return Category::from('tree')
            ->withRecursiveExpression('tree', $query)
            ->get()
            ->where('id', '!=', $this->id);
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
     * A category has a most recently active thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recentlyActiveThread()
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Count all the threads that are associated with the category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithThreadsCount($query)
    {
        DB::unprepared('
        DROP FUNCTION IF EXISTS count_threads;
              CREATE FUNCTION `count_threads`(category_id INT) RETURNS int
        BEGIN
        DECLARE threadsCount INT;
        SELECT count(id)
        INTO threadsCount
        FROM   threads
        WHERE  threads.category_id IN ( WITH recursive recursive_categories AS
                                       (
                                              SELECT initial_categories.id
                                              FROM   categories AS initial_categories
                                              WHERE  initial_categories.id=category_id
                                              UNION ALL
                                              SELECT remaining_categories.id
                                              FROM   recursive_categories
                                              JOIN   categories AS remaining_categories
                                              ON     recursive_categories.id=remaining_categories.parent_id )
                                SELECT   id
                                FROM     recursive_categories );
        return threadsCount;
        end
                        ');
        if (is_null($query->getQuery()->columns)) {
            $query->addSelect('*');
        }
        return $query->selectRaw('count_threads(categories.id) as threads_count');
    }

    /**
     * Eager load the most recently active thread for the category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithRecentlyActiveThread($query)
    {
        DB::unprepared('
        DROP FUNCTION IF EXISTS recently_active_thread;
        CREATE FUNCTION `recently_active_thread`(category_id INT) RETURNS int
        BEGIN
          DECLARE recentlyActiveThreadId INT;
          SELECT id
          INTO recentlyActiveThreadId
          FROM   threads
          WHERE  threads.category_id IN ( WITH recursive recursive_categories AS
                                         (
                                                SELECT initial_categories.id
                                                FROM   categories AS initial_categories
                                                WHERE  initial_categories.id=category_id
                                                UNION ALL
                                                SELECT remaining_categories.id
                                                FROM   recursive_categories
                                                JOIN   categories AS remaining_categories
                                                ON     recursive_categories.id=remaining_categories.parent_id )
                                  SELECT   id
                                  FROM     recursive_categories )
        ORDER BY updated_at DESC limit 1;
        return recentlyActiveThreadId;
        end
                ');
        if (is_null($query->getQuery()->columns)) {
            $query->addSelect('*');
        }
        return $query
            ->selectRaw('recently_active_thread(categories.id) as recently_active_thread_id')
            ->with(['recentlyActiveThread' => function ($query) {
                return $query->withRecentReply();
            }]);
    }

    /**
     * Count the all the replies that are associated with the category
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithRepliesCount($query)
    {
        DB::unprepared('
        DROP FUNCTION IF EXISTS count_replies;
                CREATE FUNCTION `count_replies`(category_id INT) RETURNS int
        BEGIN
                          DECLARE repliesCount INT;
                          SELECT sum(replies_count)
                          INTO repliesCount
                          FROM   threads
                          WHERE  threads.category_id IN ( WITH recursive recursive_categories AS
                                                         (
                                                                SELECT initial_categories.id
                                                                FROM   categories AS initial_categories
                                                                WHERE  initial_categories.id=category_id
                                                                UNION ALL
                                                                SELECT remaining_categories.id
                                                                FROM   recursive_categories
                                                                JOIN   categories AS remaining_categories
                                                                ON     recursive_categories.id=remaining_categories.parent_id )
                                                  SELECT   id
                                                  FROM     recursive_categories );
                        return repliesCount;
        end
                ');

        if (is_null($query->getQuery()->columns)) {
            $query->addSelect('*');
        }
        return $query->selectRaw('IFNULL(count_replies(categories.id), 0) as replies_count');
    }

    public function scopeWithDescendantCategoriesCount($query)
    {

        DB::unprepared('
        DROP FUNCTION IF EXISTS count_descendant_categories;
            CREATE FUNCTION `count_descendant_categories`(category_id INT) RETURNS int
        BEGIN
        DECLARE descendantCategoriesCount INT;
        SELECT count(id)
        INTO descendantCategoriesCount
        FROM   categories
        WHERE  categories.id IN ( WITH recursive recursive_categories AS
                                       (
                                              SELECT initial_categories.id
                                              FROM   categories AS initial_categories
                                              WHERE  initial_categories.parent_id=category_id
                                              UNION ALL
                                              SELECT remaining_categories.id
                                              FROM   recursive_categories
                                              JOIN   categories AS remaining_categories
                                              ON     recursive_categories.id=remaining_categories.parent_id )
                                SELECT   id
                                FROM     recursive_categories );
        return descendantCategoriesCount;
        end
                ');

        if (is_null($query->getQuery()->columns)) {
            $query->addSelect('*');
        }
        return $query->selectRaw('IFNULL(count_descendant_categories(categories.id), 0) as descendant_categories_count');
    }

    /**
     * Determines the path to category's image
     *
     * @param string $image
     * @return string
     */
    public function getImagePathAttribute($image)
    {
        return asset($image ?: '/images/categories/apple_logo.png');
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

    /**
     * Determine whether it has a parent
     *
     * @return boolean
     */
    public function hasParent()
    {
        return $this->parent_id != null;
    }

    /**
     * Exclude the given category
     *
     * @param Builder $query
     * @param Category $category
     * @return Builder
     */
    public function scopeExcept($query, $category)
    {
        return $query->where('id', '!=', $category->id);
    }

}