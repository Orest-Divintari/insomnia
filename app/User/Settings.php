<?php

namespace App\User;

use App\Exceptions\SettingDoesNotExistException;
use App\User;
use Carbon\Carbon;
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
     * Create a new Settings instance
     *
     * @param array $settings
     * @param User $user
     */
    public function __construct(array $settings, User $user)
    {
        $this->settings = $settings;
        $this->user = $user;
    }

    /**
     * Merge the given settings with the existing settings
     * Prevents from adding new settings
     *
     * @param array $attributes
     * @return void
     */
    public function merge($attributes)
    {
        $attributes = $this->allowed($attributes);

        $attributes = $this->setCasts($attributes);

        $this->settings = array_merge($this->settings, $attributes);

        $this->persist();
    }

    /**
     * Filter out the attributes that are not allowed
     *
     * @param array $attributes
     * @return array
     */
    public function allowed($attributes)
    {
        return Arr::only($attributes, array_keys($this->settings));
    }

    /**
     * Cast attributes
     *
     * @param array $attributes
     * @return array
     */
    public function setCasts($attributes)
    {
        if (!empty($this->setCasts)) {
            $casts = array_intersect_key($this->setCasts, $attributes);

            foreach ($casts as $attribute => $type) {
                $castMethod = $this->setCasts[$attribute];

                $value = $attributes[$attribute];

                $castValue = $this->$castMethod($value);
                $attributes[$attribute] = $castValue;
            }
        }
        return $attributes;
    }

    /**
     * Cast to boolean
     *
     * @param mixed $value
     * @return bool
     */
    protected function boolean($value)
    {
        return to_bool($value);
    }

    /**
     * Cast to datetime string
     *
     * @param mixed $value
     * @return string
     */
    protected function datetime($value)
    {
        if (empty($value)) {
            return;
        }

        if ($value instanceof Carbon) {
            return $value->format('Y-m-d');
        }

        return Carbon::parse($value)->format('Y-m-d');
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
     * @return string
     * @throws SettingDoesNotExistException
     */
    public function __get($key)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        throw new SettingDoesNotExistException("The {$key} setting does not exist.");
    }
}