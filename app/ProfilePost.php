<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfilePost extends Model
{
    const PER_PAGE = 3;

    /**
     * Relationships to always eager-laod
     *
     * @var array
     */
    protected $with = ['poster'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['date_created'];

    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * A profile post has an owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poster()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Format the date the post was created
     *
     * @return string
     */
    public function getDateCreatedAttribute()
    {
        return $this->created_at->calendar();
    }
}