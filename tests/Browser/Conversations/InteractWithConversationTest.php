<?php

namespace Tests\Browser\Conversations;

use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InteractWithConversationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_like_a_message()
    {
        $conversationStarter = create(User::class);
        $participant = create(User::class);
        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage($message['body'])
            ->create();
        $message = $conversation->messages()->first();

        $this->browse(function (Browser $browser) use (
            $conversationStarter,
            $participant,
            $message,
            $conversation
        ) {
            $browser
                ->loginAs($conversationStarter)
                ->visit(route('conversations.show', $conversation))
                ->click('@like-button')
                ->pause(500)
                ->assertVue('isLiked', true, '@like-button-component');
        });
    }
}