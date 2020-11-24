<?php

namespace Tests\Feature;

use App\Conversation;
use App\Notifications\ConversationHasNewMessage;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ConversationNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function when_a_new_message_is_added_to_conversaation_the_participants_should_receive_email_notification()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);

        ConversationFactory::withParticipants([$participant->name])
            ->create();
        Notification::assertSentTo($participant, ConversationHasNewMessage::class);
    }

    /** @test */
    public function when_a_new_message_is_added_to_conversation_the_user_who_added_the_message_should_not_receive_a_notification()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);

        ConversationFactory::withParticipants([$participant->name])
            ->create();
        Notification::assertSentTo($participant, ConversationHasNewMessage::class);
        Notification::assertNotSentTo($conversationStarter, ConversationHasNewMessage::class);
    }

    /** @test */
    public function when_a_new_message_is_added_to_conversation_the_users_who_left_permanently_the_conversation_should_not_receive_notifications()
    {
        $conversationStarter = $this->signIn();
        $john = create(User::class);
        $orestis = create(User::class, ['name' => 'orestis']);

        $conversation = create(Conversation::class, ['user_id' => $conversationStarter->id]);
        $conversation->addParticipants([$john->name, $orestis->name]);
        $conversation->leftBy($orestis);

        $conversation->addMessage('some message', $conversationStarter);

        Notification::assertSentTo($john, ConversationHasNewMessage::class);
        Notification::assertNotSentTo($conversationStarter, ConversationHasNewMessage::class);
        Notification::assertNotSentTo($orestis, ConversationHasNewMessage::class);
    }

    /** @test */
    public function when_a_new_message_is_added_to_conversation_the_users_who_hid_the_conversation_will_still_receive_a_notification()
    {
        $conversationStarter = $this->signIn();
        $john = create(User::class);
        $orestis = create(User::class, ['name' => 'orestis']);

        $conversation = create(Conversation::class, ['user_id' => $conversationStarter->id]);
        $conversation->addParticipants([$john->name, $orestis->name]);
        $conversation->hideFrom($orestis);

        $conversation->addMessage('some message', $conversationStarter);

        Notification::assertSentTo($john, ConversationHasNewMessage::class);
        Notification::assertSentTo($orestis, ConversationHasNewMessage::class);
        Notification::assertNotSentTo($conversationStarter, ConversationHasNewMessage::class);
    }
}