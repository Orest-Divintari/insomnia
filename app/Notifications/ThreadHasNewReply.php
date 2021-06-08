<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThreadHasNewReply extends Notification
{
    use Queueable;

    public $thread;
    public $reply;
    /**
     * Create a new notification instance.
     *
     * @param $thread
     * @param $reply
     * @return void
     */
    public function __construct($thread, $reply)
    {
        $this->thread = $thread;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = $notifiable->preferences()->thread_reply_created;

        $prefersEmail = $notifiable
            ->subscription($this->thread->id)
            ->prefers_email;

        if ($prefersEmail) {
            $channels[] = 'mail';
        }

        return $channels;
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
            ->view('emails.subscription.notify-thread-subscribers', [
                'thread' => $this->thread,
                'reply' => $this->reply,
                'view_type' => 'notify-thread-subscribers',
            ]);
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
            'thread' => $this->thread,
            'reply' => $this->reply,
            'type' => 'thread-reply-notification',
            'triggerer' => $this->reply->poster,
            'redirectTo' => route('replies.show', $this->reply),
        ];
    }
}