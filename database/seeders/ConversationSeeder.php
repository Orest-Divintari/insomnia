<?php

namespace Database\Seeders;

use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\MessageFactory;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    use RandomModels, AuthenticatesUsers;

    const NUMBER_OF_PARTICIPANTS = 1;

    const NUMBER_OF_MESSAGES = 1;

    const NUMBER_OF_USERS = 1;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->randomUsers(static::NUMBER_OF_USERS)->each(function ($user) {

            $this->signIn($user);

            $participants = $this->randomUsersExcept(static::NUMBER_OF_PARTICIPANTS, $user);

            // CREATE CONVERSATIONS
            $conversation = ConversationFactory::by($user)
                ->withParticipants($participants->pluck('name')->toArray())
                ->create();

            // ADD MESSAGES TO EACH CONVERSATION
            $participants->each(function ($participant) use ($conversation) {
                $this->signIn($participant);
                MessageFactory::by($participant)
                    ->toConversation($conversation)
                    ->create();
            });

        });
    }
}