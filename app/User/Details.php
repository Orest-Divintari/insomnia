<?php

namespace App\User;

use App\User;
use App\User\Settings;

class Details extends Settings
{
    /**
     * The attributes that should be cast to native types before saving in the database.
     *
     * @var array
     */
    protected $setCasts = [
        'birth_date' => 'datetime',
    ];

    /**
     * Persist the details
     *
     * @return void
     */
    public function persist()
    {
        $this->user->update(['details' => $this->settings]);
    }
}