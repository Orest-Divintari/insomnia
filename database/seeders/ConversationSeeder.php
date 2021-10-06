<?php

namespace Database\Seeders;

use Facades\Tests\Setup\ConversationFactory;
use Facades\Tests\Setup\MessageFactory;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    use RandomModels, AuthenticatesUsers;

    const NUMBER_OF_PARTICIPANTS = 2;

    const NUMBER_OF_MESSAGES = 1;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->randomUsers(1000)->each(function ($user) {

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