<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentHasNewLike extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $commentPoster;
    public $profilePost;
    public $profileOwner;
    public $liker;

    /**
     * Create a new notification instance.
     *
     * @param User $liker
     * @param Like $like
     * @param Reply $comment
     * @param User $commentPoster
     * @param ProfilePost $profilePost
     * @param User $profileOwner
     *
     * @return void
     */
    public function __construct($liker, $like, $comment, $commentPoster, $profilePost, $profileOwner)
    {
        $this->liker = $liker;
        $this->like = $like;
        $this->comment = $comment;
        $this->commentPoster = $commentPoster;
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
            'profileOwner' => $this->profileOwner,
            'commentPoster' => $this->commentPoster,
            'profilePost' => $this->profilePost,
            'comment' => $this->comment,
            'liker' => $this->liker,
            'like' => $this->like,
            'type' => 'commentLike',
        ];
    }
}