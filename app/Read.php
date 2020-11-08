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
}