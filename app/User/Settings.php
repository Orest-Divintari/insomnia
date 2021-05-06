<?php

namespace App\User;

use App\User;
use Exception;
use Illuminate\Support\Arr;

abstract class Settings
{
    /**
     * The user instance
     *
     * @var User
     */
    protected $user;

    /**
     * The list of user settings
     *
     * @var array
     */
    protected $settings;

    /**
     * The list of allowed settings
     *
     * @var array
     */
    protected $allowed;

    /**
     * The list of default settings
     *
     * @var array
     */
    protected $default;

    /**
     * Create a new Settings instance
     *
     * @param array $settings
     * @param User $user
     */
    public function __construct(array $settings, User $user)
    {
        $this->settings = $settings;
        $this->user = $user;
        $this->allowed = array_keys($this->default);
    }

    /**
     * Merge the given settings with the existing settings
     * Prevents from adding new settings
     *
     * @param array $attributes
     * @return mixed
     */
    public function merge($attributes)
    {
        $this->settings = array_merge(
            $this->settings,
            Arr::only($attributes, $this->allowed)
        );

        $this->persist();
    }

    /**
     * Persist the settings
     *
     * @return mixed
     */
    abstract public function persist();

    /**
     * Determine if the given settings exists.
     *
     * @param  string $key
     * @return boolean
     */
    protected function has($key)
    {
        return array_key_exists($key, $this->settings);
    }

    /**
     * Retrieve the given detail
     *
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        $value = Arr::get($this->settings, $key);

        return $value;
    }

    /**
     * Retrieve an array of all settings.
     *
     * @return array
     */
    public function all()
    {
        return $this->settings;
    }

    /**
     * Magic property access for settings.
     *
     * @param  string $key
     * @throws Exception
     * @return
     */
    public function __get($key)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        throw new Exception("The {$key} setting does not exist.");
    }

    /**
     * Set settings to default
     *
     * @return void
     */
    public function setDefault()
    {
        $this->merge($this->default);
    }

    /**
     * Get the default settings
     *
     * @return void
     */
    public function getDefault()
    {
        return $this->default;
    }
}