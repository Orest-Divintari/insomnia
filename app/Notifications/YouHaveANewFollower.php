<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class YouHaveANewFollower extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The user who started following
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
     * The date of the follow
     *
     * @param Carbon $followDate
     */
    public $followDate;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($follower, $following, $followDate)
    {
        $this->follower = $follower;
        $this->following = $following;
        $this->followDate = $followDate;
        $this->onQueue('notifications');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->preferences()->user_followed_you;
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
            'follow_date_created' => $this->followDate->calendar(),
            'follower_id' => $this->follower->id,
            'triggerer' => $this->follower,
            'redirectTo' => route('profiles.show', $this->follower),
        ];
    }
}