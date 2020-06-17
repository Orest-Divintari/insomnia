<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Thread extends Model
{

    /**
     * Relationships to always eager-load
     *
     * @var array
     */
    protected $with = ['recentReply', 'poster'];

    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = ['title', 'slug', 'body', 'user_id', 'category_id'];

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
     * Get the string api path for the thread
     *
     * @return string
     */
    public function api_path()
    {
        return '/api/threads/' . $this->slug;
    }

    /**
     * Get the replies associated with the thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function replies()
    {
        return $this->morphMany(Reply::class, 'repliable');
    }

    public function recentReply()
    {
        return $this->morphOne(Reply::class, 'repliable')->latest('updated_at');
    }

    /**
     * Get the user that created the thread
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * A thread belongs to a category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Assert the slug is unique
     *
     * @param string $slug
     * @return void
     */
    public function setSlugAttribute($slug)
    {
        if (Thread::where('slug', $slug)->exists()) {
            $slug = $this->createUnique($slug);
        }
        $this->attributes['slug'] = $slug;

    }

    /**
     * Create a unique slug
     *
     * @param string $slug
     * @return string $slug
     */
    protected function createUnique($slug)
    {
        $counter = 2;
        $originalSlug = $slug;
        while (Thread::whereSlug($slug)->exists()) {

            $slug = "{$originalSlug}.{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Shorten the length of the title
     *
     * @return string
     */
    public function getShortTitleAttribute()
    {
        return Str::limit($this->title, config('constants.thread.title_limit'), '');
    }

    /**
     * Transform the date it was updated to human readable datetime
     *
     * @return string
     */
    public function getDateUpdatedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

    /**
     * Transform the date it was created to human readable datetime
     *
     * @return string
     */
    public function getDateCreatedAttribute()
    {
        return $this->updated_at->diffForHumans();
    }

}