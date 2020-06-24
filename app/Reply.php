<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class Reply extends Model
{

    const PER_PAGE = 3;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['date_updated', 'date_created'];

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
    protected $with = ['poster'];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['thread'];

    /**
     * Touch the Thread relationship
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function thread()
    {
        return $this->belongsTo(Thread::class, 'repliable_id');
    }

    /**
     * A reply belongs to a repliable model
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function repliable()
    {
        return $this->morphTo();
    }

    /**
     * A reply belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Transform the date that it was updated to readable format
     *
     * @return string
     */
    public function getDateUpdatedAttribute()
    {
        return $this->updated_at->calendar();
    }

    /**
     * Transform the date that it was created to readable format
     *
     * @return string
     */
    public function getDateCreatedAttribute()
    {
        return $this->created_at->calendar();
    }

    /**
     * Clean the body from malicious context
     *
     * @param string $body
     * @return string
     */
    public function getBodyAttriute($body)
    {
        return Purify::clean($body);
    }

}