<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfileHasNewPost extends Notification
{
    use Queueable;

    public $postPoster;
    public $profilePost;
    public $profileOwner;
    /**
     * Create a new notification instance.
     *
     * @param ProfilePost $post
     * @param User $profilePoster
     * @param User $profileOwner
     *
     * @return void
     */
    public function __construct($profilePost, $postPoster, $profileOwner)
    {
        $this->postPoster = $postPoster;
        $this->profilePost = $profilePost;
        $this->profileOwner = $profileOwner;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->preferences()->profile_post_created;
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
            'postPoster' => $this->postPoster,
            'profilePost' => $this->profilePost,
            'profileOwner' => $this->profileOwner,
            'type' => "profile-post-notification",
            'triggerer' => $this->postPoster,
            'redirectTo' => route('profile-posts.show', $this->profilePost),
        ];
    }
}