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

    public $description;
    public $subject;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($description, $subject = null)
    {
        $this->description = $description;
        $this->subject = $subject;
    }
}