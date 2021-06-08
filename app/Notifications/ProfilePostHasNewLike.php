<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfilePostHasNewLike extends Notification
{
    use Queueable;

    public $profilePost;
    public $profileOwner;
    public $poster;
    public $liker;
    public $like;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($profilePost, $poster, $profileOwner, $liker, $like)
    {
        $this->profilePost = $profilePost;
        $this->profileOwner = $profileOwner;
        $this->poster = $poster;
        $this->liker = $liker;
        $this->like = $like;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->preferences()->profile_post_liked;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
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
            'type' => 'profile-post-like-notification',
            'liker' => $this->liker,
            'like' => $this->like,
            'profilePost' => $this->profilePost,
            'profileOwner' => $this->profileOwner,
            'poster' => $this->poster,
            'triggerer' => $this->liker,
            'redirectTo' => route('profile-posts.show', $this->profilePost),
        ];
    }
}