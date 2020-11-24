<?php

namespace App\Notifications;

use App\Conversation;
use App\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConversationHasNewMessage extends Notification
{
    use Queueable;

    protected $conversation;
    protected $meessage;

    /**
     * Create a new notification instance.
     *
     * @param Conversation $conversation
     * @param Reply $message
     * @return void
     */
    public function __construct(Conversation $conversation, Reply $message)
    {
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
        return ['mail'];
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
            //
        ];
    }
}