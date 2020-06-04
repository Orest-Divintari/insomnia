<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{

    /**
     * A reply belongs to a repliable model
     *
     * @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function repliable()
    {
        return $this->morphTo();
    }

}