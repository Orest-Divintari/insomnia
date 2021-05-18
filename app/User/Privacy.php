<?php

namespace App\User;

use App\User\Settings;

class Privacy extends Settings
{
    const ALLOW_MEMBERS = 'members';
    const ALLOW_NOONE = 'noone';
    const ALLOW_FOLLOWING = 'following';

    /**
     * The attributes that should be cast to native types before saving in the database.
     *
     * @var array
     */
    protected $setCasts = [
        'show_current_activity' => 'boolean',
        'show_birth_date' => 'boolean',
        'show_birth_year' => 'boolean',
    ];

    /**
     * Persist the privacy settings
     *
     * @return void
     */
    public function persist()
    {
        $this->user->update(['privacy' => $this->settings]);
    }

    /**
     * Determine whether the given ability is allowed by the privacy settings
     *
     * @param string $ability
     * @return bool
     */
    public function allows($ability)
    {
        $permission = $this->user->privacy()->$ability;

        if (method_exists($this, $permission)) {
            $permission = $this->$permission();
        }

        return to_bool($permission);
    }

    /**
     * Give permission to noone
     *
     * @return bool
     */
    protected function noone()
    {
        return false;
    }

    /**
     * Give permission only to members
     *
     * @return boolean
     */
    protected function members()
    {
        return auth()->check();
    }

    /**
     * Give permission only if the user is following the authenticated user
     *
     * @return bool
     */
    protected function following()
    {
        return auth()->check() ?
        $this->user->following(auth()->user()) :
        false;
    }
}