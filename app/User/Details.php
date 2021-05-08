<?php

namespace App\User;

use App\User;
use App\User\Settings;
use Illuminate\Support\Arr;

class Details extends Settings
{
    /**
     * The list of default details
     *
     * @var array
     */
    protected $default = [
        'location' => '',
        'birth_date' => '',
        'website' => '',
        'gender' => '',
        'occupation' => '',
        'about' => '',
        'skype' => '',
        'google_talk' => '',
        'facebook' => '',
        'twitter' => '',
        'instagram' => '',
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