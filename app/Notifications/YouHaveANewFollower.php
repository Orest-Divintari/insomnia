<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class YouHaveANewFollower extends Notification
{
    use Queueable;

    /**
     * The user who started following another user
     *
     * @var User
     */
    public $follower;

    /**
     * The user who has a new follower
     *
     * @var User
     */
    public $following;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($follower, $following)
    {
        $this->follower = $follower;
        $this->following = $following;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'follower' => $this->follower,
            'followingUser' => $this->following,
            'type' => 'follow-notification',
            'follower_id' => $this->follower->id,
        ];
    }
}