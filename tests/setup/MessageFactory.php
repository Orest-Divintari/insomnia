<?php

namespace Tests\Setup;

use App\Conversation;
use App\Reply;
use App\User;

class MessageFactory
{

    protected $user;

    public function create($attributes = [])
    {
        $this->user = $this->user ?: factory(User::class)->create();

        $conversation = $this->createConversation($attributes);

        $message = factory(Reply::class)->create(
            array_merge(
                [
                    'user_id' => $this->user->id,
                    'repliable_id' => $conversation->id,
                    'repliable_type' => Conversation::class,
                ],
                $attributes
            ));

        return $message;
    }

    public function createMany($count = 1, $attributes = [])
    {
        $this->user = $this->user ?: factory(User::class)->create();

        $conversation = factory(Conversation::class)->create();

        $messages = factory(Reply::class, $count)->create(
            array_merge(
                [
                    'user_id' => $this->user->id,
                    'repliable_id' => $conversation->id,
                    'repliable_type' => Conversation::class,
                ],
                $attributes
            ));

        return $messages;
    }

    public function createConversation($attributes)
    {
        if (array_key_exists('repliable_id', $attributes)) {
            $conversation = Conversation::find($attributes['repliable_id']);
        } else {
            $conversation = create(Conversation::class);
        }
        return $conversation;
    }

    public function by($user)
    {
        $this->user = $user;
        return $this;
    }

}