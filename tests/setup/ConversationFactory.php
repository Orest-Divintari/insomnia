<?php

namespace Tests\Setup;

use App\Conversation;
use App\Message;
use App\User;

class ConversationFactory
{

    protected $participants = [];
    protected $message;
    protected $slug;

    protected $user = null;

    public function create($title = null)
    {

        $conversation = create(
            Conversation::class,
            ['title' => $title ?: 'some title']
        );

        if (empty($this->participants)) {
            $this->withParticipants();
        }
        if (is_null($this->message)) {
            $this->withMessage();
        }

        $conversation->addParticipants($this->participants);
        $conversation->addMessage($this->message);

        return $conversation;
    }

    /**
     * Set the message for the conversation
     *
     * @param string $message
     * @return ConversationFactory
     */
    public function withMessage($message = null)
    {
        $this->message = $message ?: 'some message';
        return $this;
    }

    /**
     * Set the username of an existing user
     *
     * @param User $user
     * @return ConversationFactory
     */
    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set the name of existing participants
     *
     * @param string[] $participants
     * @return ConversationFactory
     */
    public function withParticipants($participants = [])
    {
        if (empty($participants)) {
            $this->participants = [create(User::class)->name];
        } else {
            $this->participants = $participants;
        }

        return $this;
    }

}