<?php

namespace App\Events\Activity;

class UserViewedPage
{
    const FORUM = "Viewing forum list";
    const PROFILE = "Viewing profile";
    const THREAD = 'Viewing thread';
    const CONVERSATION = 'Engaged in conversation';
    const CATEGORY = 'Viewing category';
    const REGISTRATION = "Registering";

    /**
     * The description of the activity
     *
     * @var string
     */
    public $description;

    /**
     * The specific model that the user is acting on
     *
     * @var string|null
     */
    public $subject;

    /**
     * Create a new event instance.
     *
     * @param string $description
     * @param string|null $subject
     * @return void
     */
    public function __construct($description, $subject = null)
    {
        $this->description = $description;
        $this->subject = $subject;
    }
}