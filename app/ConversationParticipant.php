<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'admin' => 'boolean',
    ];
}