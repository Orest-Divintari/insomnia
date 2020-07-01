<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThreadHasNewReply extends Notification implements ShouldQueue
{
    use Queueable;

    protected $thread;
    protected $reply;
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
            'type' => 'reply',
        ];
    }

    public function createMessage()
    {
        return '<a class="text-blue-mid">' . $this->reply->poster->name . '</a>' . 'replied to the thread -' . '<a class="text-blue-mid" href="' . route('threads.show', $this->thread) . '>' . $this->thread->title . '</a>';
    }
}