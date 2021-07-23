<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ignoration extends Model
{
    /**
     * Don't auto-apply mass assignment protection.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * It has an ignorable model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function ignorable()
    {
        return $this->morphTo();
    }
}