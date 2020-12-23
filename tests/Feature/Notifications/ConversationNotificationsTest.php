<?php

namespace Tests\Feature\Notifications;

use App\Conversation;
use App\Notifications\ConversationHasNewMessage;
use App\Notifications\MessageHasNewLike;
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
    public function when_a_new_message_is_added_to_conversation_the_participants_should_receive_email_notification()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();

        Notification::assertSentTo(
            $participant,
            function (ConversationHasNewMessage $notification, $channels)
             use (
                $conversation,
                $message
            ) {
                return $notification->conversation->id == $conversation->id
                && $notification->message->id == $message->id;
            }
        );
    }

    /** @test */
    public function when_a_new_message_is_added_to_conversation_the_user_who_added_the_message_should_not_receive_a_notification()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])
            ->create();
        $message = $conversation->messages()->first();

        Notification::assertSentTo(
            $participant,
            function (ConversationHasNewMessage $notification, $channels)
             use (
                $conversation,
                $message
            ) {
                return $notification->conversation->id == $conversation->id
                && $notification->message->id == $message->id;
            }
        );
        Notification::assertNotSentTo(
            $conversationStarter,
            ConversationHasNewMessage::class
        );
    }

    /** @test */
    public function when_a_new_message_is_added_to_conversation_the_users_who_left_permanently_the_conversation_should_not_receive_notifications()
    {
        $conversationStarter = $this->signIn();
        $john = create(User::class);
        $orestis = create(User::class, ['name' => 'orestis']);
        $conversation = create(
            Conversation::class,
            ['user_id' => $conversationStarter->id]
        );
        $conversation->addParticipants([
            $john->name,
            $orestis->name,
        ]);
        $conversation->leftBy($orestis);

        $message = $conversation->addMessage(
            'some message',
            $conversationStarter
        );

        Notification::assertSentTo(
            $john,
            function (ConversationHasNewMessage $notification, $channels)
             use (
                $conversation,
                $message
            ) {
                return $notification->conversation->id == $conversation->id
                && $notification->message->id == $message->id;
            }
        );
        Notification::assertNotSentTo(
            $conversationStarter,
            ConversationHasNewMessage::class
        );
        Notification::assertNotSentTo(
            $orestis,
            ConversationHasNewMessage::class
        );
    }

    /** @test */
    public function when_a_new_message_is_added_to_conversation_the_users_who_hid_the_conversation_will_still_receive_a_notification()
    {
        $conversationStarter = $this->signIn();
        $john = create(User::class);
        $orestis = create(User::class, ['name' => 'orestis']);
        $conversation = create(
            Conversation::class,
            ['user_id' => $conversationStarter->id]
        );
        $conversation->addParticipants([
            $john->name,
            $orestis->name,
        ]);
        $conversation->hideFrom($orestis);
        $message = $conversation->addMessage(
            'some message',
            $conversationStarter
        );

        Notification::assertSentTo(
            $john,
            ConversationHasNewMessage::class
        );
        Notification::assertSentTo(
            $orestis,
            ConversationHasNewMessage::class
        );
        Notification::assertNotSentTo(
            $conversationStarter,
            ConversationHasNewMessage::class
        );
    }

    /** @test */
    public function when_a_conversation_message_is_liked_then_the_poster_of_the_message_receives_database_notification()
    {
        $conversationStarter = $this->signIn();
        $liker = create(User::class);
        $conversation = ConversationFactory::withParticipants([$liker->name])->create();
        $message = $conversation->messages->first();
        $this->signIn($liker);

        $like = $message->likedBy($liker);

        Notification::assertSentTo(
            $conversationStarter,
            MessageHasNewLike::class,
            function ($notification, $channels) use (
                $message,
                $liker,
                $like,
                $conversation
            ) {
                return $notification->message->id == $message->id
                && $notification->like->id == $like->id
                && $notification->liker->id == $liker->id
                && $notification->conversation->id == $conversation->id;
            });
    }
}