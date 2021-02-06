<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class MessageHasNewLike extends Notification
{

    public $like;
    public $liker;
    public $conversation;
    public $message;

    /**
     * Create a new notification instance.
     *
     * @param Like $like
     * @param User $liker
     * @param Conversation $conversation
     * @param Message $message
     */
    public function __construct($like, $liker, $conversation, $message)
    {
        $this->like = $like;
        $this->liker = $liker;
        $this->conversation = $conversation;
        $this->message = $message;
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
            'message' => $this->message,
            'conversation' => $this->conversation,
            'liker' => $this->liker,
            'like' => $this->like,
            'type' => 'message-like-notification',
        ];
    }
}
