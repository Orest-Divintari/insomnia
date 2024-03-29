<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReplyHasNewLike extends Notification implements ShouldQueue
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
        return $notifiable->preferences()->thread_reply_liked;
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
            'triggerer' => $this->liker,
            'redirectTo' => route('replies.show', $this->reply),
        ];
    }
}