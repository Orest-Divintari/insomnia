<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YouHaveBeenMentionedInAProfilePost extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        public $profilePost,
        public $profilePostPoster,
        public $profileOwner
    ) {
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
        if ($notifiable->isNot($this->profileOwner)) {
            return $notifiable->preferences()->mentioned_in_profile_post;
        }

        return array_diff(
            $notifiable->preferences()->mentioned_in_profile_post,
            $notifiable->preferences()->profile_post_created
        );
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
            'postPoster' => $this->profilePostPoster,
            'triggerer' => $this->profilePostPoster,
            'profileOwner' => $this->profileOwner,
            'profilePost' => $this->profilePost,
            'redirectTo' => route('profile-posts.show', $this->profilePost),
            'type' => 'profile-post-mention-notification',
        ];
    }
}