<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReplyHasNewLike extends Notification
{
    use Queueable;

    public $reply;
    public $thread;
    public $like;
    public $liker;
    /**
     * Create a new notification instance.
     *
     * @param User $liker
     * @param Like $like
     * @param Thread $thread
     * @param Reply $reply
     *
     * @return void
     */
    public function __construct($liker, $like, $thread, $reply)
    {
        $this->liker = $liker;
        $this->like = $like;
        $this->reply = $reply;
        $this->thread = $thread;
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

    /**b
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'reply' => $this->reply,
            'type' => 'reply-like-notification',
            'liker' => $this->liker,
            'like' => $this->like,
        ];
    }
}