<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Read extends Model
{
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Fetch the model that was read
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function readable()
    {
        return $this->morphTo();
    }

    /**
     * Fetch the read record for the given user
     *
     * @param User $user
     * @return Builder
     */
    public function scopeByUser($query, $user)
    {
        return $query->where('user_id', $user->id);
    }

}