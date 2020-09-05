<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProfilePostHasNewComment extends Notification implements ShouldQueue
{
    use Queueable;

    protected $profilePost;
    protected $comment;
    protected $commentPoster;
    protected $profileOwner;

    /**
     * Create a new notification instance.
     *
     * @param ProfilePost $profilePost
     * @param Reply $comment
     * @param User $commentPoster
     * @param User $profileOwner
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
        return ['mail', 'database'];
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
        ];
    }
}