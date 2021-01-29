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
        return $notifiable
            ->subscription($this->thread->id)
            ->prefers_email ? ['mail', 'database'] : ['database'];
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
            ->view('emails.subscription.notify_thread_subscribers', [
                'thread' => $this->thread,
                'reply' => $this->reply,
                'view_type' => 'notify_thread_subscribers',
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
        ];
    }
}