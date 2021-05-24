<?php

namespace Tests\Feature\Notifications;

use App\Conversation;
use App\Notifications\ConversationHasNewMessage;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ConversationNotificationsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function users_may_prefer_to_receive_email_every_time_they_receive_a_new_conversation_message()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $attributes = [
            'title' => $this->faker()->sentence(),
            'message' => $this->faker()->text(),
            'participants' => $participant->name,
        ];

        $this->post(route('conversations.store'), $attributes);

        $conversation = $participant->conversations()->first();
        Notification::assertSentTo($participant,
            function (ConversationHasNewMessage $notification, $channels) use ($conversation, $participant) {
                return empty(array_diff_assoc($participant->preferences()->message_created, $channels)) &&
                $conversation->id == $notification->conversation->id;
            });
    }

    /** @test */
    public function users_may_disable_email_notifications_when_they_receive_a_new_converasation_message()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $participant->preferences()->merge(['message_created' => []]);
        $attributes = [
            'title' => $this->faker()->sentence(),
            'message' => $this->faker()->text(),
            'participants' => $participant->name,
        ];

        $this->post(route('conversations.store'), $attributes);

        $conversation = $participant->conversations()->first();
        Notification::assertSentTo($participant,
            function (ConversationHasNewMessage $notification, $channels) use ($conversation, $participant) {
                return empty($channels) &&
                $conversation->id == $notification->conversation->id;
            });
    }

    /** @test */
    public function the_users_who_add_a_message_to_a_conversation_should_not_receive_notification_about_their_own_message()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $attributes = [
            'title' => $this->faker()->sentence(),
            'message' => $this->faker()->text(),
            'participants' => $participant->name,
        ];

        $this->post(route('conversations.store'), $attributes);

        $conversation = $conversationStarter->conversations()->first();
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
            ['body' => 'some message'],
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
            ['body' => 'some message'],
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

}