<?php

namespace App\Traits;

use App\User\Privacy;

trait HandlesPrivacy
{

    /**
     * Get privacy settings instance
     *
     * @return App\User\Privacy
     */
    public function privacy()
    {
        return new Privacy($this->privacy, $this);
    }

    /**
     * Determine whether the givne setting is true
     *
     * @param string $setting
     * @return boolean
     */
    public function allows($setting)
    {
        return $this->privacy()->allows($setting);
    }

    /**
     * Determine whether the given setting is false
     *
     * @param string $setting
     * @return boolean
     */
    public function denies($setting)
    {
        return !$this->allows($setting);
    }

    /**
     * Allow following users the given settings
     *
     * @param array ...$settings
     * @return void
     */
    public function allowFollowing(...$settings)
    {
        $settings = $this->setSettingsTo($settings, 'following');

        $this->privacy()->merge($settings);
    }

    /**
     * Set to true the given settings
     *
     * @param string[] ...$settings
     * @return void
     */
    public function allow(...$settings)
    {
        $settings = $this->setSettingsTo($settings, true);

        $this->privacy()->merge($settings);
    }

    /**
     * Set to false the given settings
     *
     * @param string[] ...$settings
     * @return void
     */
    public function disallow(...$settings)
    {
        $settings = $this->setSettingsTo($settings, false);

        $this->privacy()->merge($settings);
    }

    /**
     * Allow to noone the given settings
     *
     * @param string[] ...$settings
     * @return void
     */
    public function allowNoone(...$settings)
    {
        $settings = $this->setSettingsTo($settings, 'noone');

        $this->privacy()->merge($settings);
    }

    /**
     * Allow to members the given settings
     *
     * @param string[] ...$settings
     * @return void
     */
    public function allowMembers(...$settings)
    {
        $settings = $this->setSettingsTo($settings, 'members');

        $this->privacy()->merge($settings);
    }

    /**
     * Set a value for all given settings
     *
     * @param string[] $settingKeys
     * @param mixed $settingValue
     * @return array
     */
    protected function setSettingsTo($settingKeys, $settingValue)
    {
        return collect($settingKeys)
            ->flip()
            ->map(fn($settingKey) => $settingValue)
            ->toArray();
    }

}