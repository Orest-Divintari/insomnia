<?php

namespace App\User;

use App\User\Settings;

class Preferences extends Settings
{

    /**
     * The attributes that should be cast to native types before saving in the database.
     *
     * @var array
     */
    protected $setCasts = [
        'subscribe_on_creation' => 'boolean',
        'subscribe_on_creation_with_email' => 'boolean',
        'subscribe_on_interaction' => 'boolean',
        'subscribe_on_interaction_with_email' => 'boolean',
    ];

    public function persist()
    {
        $this->user->update(['preferences' => $this->settings]);
    }

    public function notifications($type)
    {
        return $this->settings['notifications'][$type];
    }
}