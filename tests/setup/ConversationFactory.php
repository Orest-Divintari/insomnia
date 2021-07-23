<?php

namespace Tests\Setup;

use App\Message;
use App\Models\Conversation;
use App\Models\User;
use Faker\Generator as Faker;
use Tests\Setup\FactoryInterface;

class ConversationFactory extends Factory implements FactoryInterface
{

    /**
     * Faker generator
     *
     * @var Faker
     */
    protected $faker;

    /**
     * Participant names
     *
     * @var string[]
     */
    protected $participants = [];

    /**
     * Conversation message
     *
     * @var string
     */
    protected $message;

    /**
     * Converastion slug
     *
     * @var string
     */
    protected $slug;

    /**
     * Conversation starter
     *
     * @var User
     */
    protected $user = null;

    /**
     * Create a new instance
     *
     * @param Faker $faker
     */
    public function __construct(Faker $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Create a new conversation
     *
     * @param string $title
     * @return Conversation
     */
    public function create($attributes = [])
    {
        $this->attributes = $attributes;

        $conversation = $this->createConversation($attributes);

        $this->addParticipants($conversation);

        $this->addMessage($conversation);

        $this->resetAttributes();

        return $conversation;
    }

    /**
     * Create many conversations
     *
     * @param integer $count
     * @param array $attributes
     * @return Collection
     */
    public function createMany($count = 1, $attributes = [])
    {
        $this->attributes = $attributes;

        $conversations = $this->createConversation($attributes, $count);

        $conversations->each(function ($conversation) {
            $this->addParticipants($conversation);
            $this->addMessage(['body' => $conversation]);
        });

        $this->resetAttributes();

        return $conversations;
    }

    /**
     * Create one or many conversation instances
     *
     * @param $attributes[]
     * @param integer $count
     * @return Conversation||Collection
     */
    protected function createConversation($attributes, $count = 1)
    {
        $conversations = Conversation::factory()->count($count)->create(
            array_merge(
                [
                    'user_id' => $this->userId(),
                    'title' => $this->faker->sentence(),
                ],
                $attributes
            ));

        if ($count == 1) {
            return $conversations->first();
        }

        return $conversations;
    }

    /**
     * Add participants to the given conversation
     *
     * @param Conversation $conversation
     * @return void
     */
    protected function addParticipants($conversation)
    {
        if (empty($this->participants)) {
            $this->withParticipants();
        }

        if (!auth()->check()) {
            $this->participants[] = $this->user->name;
        }

        $conversation->addParticipants($this->participants);
    }

    /**
     * Add a message to the given conversation
     *
     * @param Conversation $conversation
     * @return void
     */
    protected function addMessage($conversation)
    {
        if (is_null($this->message)) {
            $this->withMessage();
        }

        $conversation->addMessage(['body' => $this->message], $this->user);
    }

    /**
     * Get the user id
     *
     * @return int
     */
    protected function userId()
    {
        return $this->user ? $this->user->id : auth()->id();
    }

    /**
     * Set the message for the conversation
     *
     * @param string $message
     * @return ConversationFactory
     */
    public function withMessage($message = null)
    {
        $this->message = $message ?: app(Faker::class)->sentence();
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