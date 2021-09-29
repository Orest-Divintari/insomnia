<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YourProfilePostHasNewComment extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($profilePost, $comment, $commentPoster, $profileOwner)
    {
        $this->profilePost = $profilePost;
        $this->comment = $comment;
        $this->commentPoster = $commentPoster;
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
        return CommentNotification::channels(
            $notifiable,
            $this->comment,
            $notifiable->preferences()
                ->comment_on_your_profile_post_created
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
            'commentPoster' => $this->commentPoster,
            'profilePost' => $this->profilePost,
            'postPoster' => $this->profilePost->poster,
            'comment' => $this->comment,
            'profileOwner' => $this->profileOwner,
            'type' => 'post-comment-notification',
            'triggerer' => $this->commentPoster,
            'redirectTo' => route('comments.show', $this->comment),
        ];
    }
}