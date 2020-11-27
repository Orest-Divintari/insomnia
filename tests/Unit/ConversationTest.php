<?php

namespace Tests\Unit;

use App\Conversation;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_conversation_can_have_many_participants()
    {
        $conversationStarter = $this->signIn();
        $participantB = create(User::class);

        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants(array($participantB->name))
            ->create();

        $this->assertCount(2, $conversation->participants);
    }

    /** @test */
    public function a_single_participant_might_be_added_to_conversation()
    {
        $conversationStarter = $this->signIn();

        $participant = create(User::class);
        $conversation = create(Conversation::class);

        $conversation->addParticipants(array($participant->name));

        $this->assertEquals(
            $conversation->id,
            $participant->conversations()->first()->id
        );
    }

    /** @test */
    public function multiple_participants_might_be_added_to_conversation()
    {
        $conversationStarter = $this->signIn();
        $participantA = create(User::class);
        $participantB = create(User::class);
        $participantNames = [$participantA->name, $participantB->name];

        $conversation = create(Conversation::class);
        $conversation->addParticipants($participantNames);

        $participantNames = collect($participantNames)
            ->merge(auth()->user()->name);

        $this->assertTrue(
            $conversation
                ->participants
                ->pluck('name')
                ->every(function ($value, $key) use ($participantNames) {
                    return $participantNames->contains($value);
                })
        );
    }

    /** @test */
    public function a_conversation_has_messages()
    {
        $conversationStarter = $this->signIn();

        $message = ['body' => 'some message'];
        $conversation = ConversationFactory::by($conversationStarter)
            ->withMessage($message['body'])
            ->create();

        $this->assertCount(1, $conversation->messages);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $conversation->id,
            'repliable_type' => Conversation::class,
            'body' => $message['body'],
            'user_id' => auth()->id(),
        ]);
    }

    /** @test */
    public function a_conversation_can_find_the_ids_of_participants_given_the_usernames()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::by($conversationStarter)
            ->create();
        $particiapntIds = $conversation
            ->getParticipantIds(array($conversationStarter->name));

        $this->assertContains($conversationStarter->id, $particiapntIds);
    }

    /** @test */
    public function a_conversation_has_a_starter_user()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::by($conversationStarter)->create();

        $this->assertEquals(
            $conversation->starter->id,
            $conversationStarter->id
        );
    }

    /** @test */
    public function a_conversation_knows_which_one_is_the_most_recent_message()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();
        $conversation->
            messages()
            ->first()
            ->update(
                ['created_at' => Carbon::now()->subHour()]
            );

        $newMessage = $conversation->addMessage('new message');

        $conversation = Conversation::whereSlug($conversation->slug)
            ->withRecentMessage()
            ->first();

        $this->assertEquals(
            $newMessage->id,
            $conversation->recentMessage->id
        );
    }

    /** @test */
    public function a_conversation_can_get_the_type_of_the_model()
    {
        $this->signIn();

        $conversation = create(Conversation::class);

        $this->assertEquals(
            $conversation->type,
            'conversation'
        );
    }

    /** @test */
    public function a_conversation_can_be_marked_as_read_by_many_participants()
    {
        $user = $this->signIn();
        $conversation = create(Conversation::class);

        $user->readConversation($conversation);

        $this->assertCount(1, $conversation->reads);

        $anotherUser = $this->signIn();
        $conversation->addParticipants([$anotherUser->name]);

        $anotherUser->readConversation($conversation);

        $this->assertCount(2, $conversation->fresh()->reads);
    }

    /** @test */
    public function a_conversation_is_updated_when_a_new_message_is_added()
    {
        $this->signIn();

        $conversationDate = Carbon::now()->subDay();
        $conversation = create(
            Conversation::class,
            ['updated_at' => $conversationDate]
        );
        $this->assertFalse(
            $conversation->fresh()->updated_at > $conversationDate
        );

        $conversation->addMessage('some message');
        $this->assertTrue(
            $conversation->fresh()->updated_at > $conversationDate
        );
    }

    /** @test */
    public function a_conversation_can_be_hidden_from_a_user()
    {
        $user = $this->signIn();
        createMany(Conversation::class, 2);
        $this->assertCount(2, $user->conversations);

        $conversation = Conversation::first();
        $conversation->hideFrom($user);
        $this->assertCount(1, $user->fresh()->conversations);
    }

    /** @test */
    public function a_user_may_leave_a_conversation()
    {
        $user = $this->signIn();
        createMany(Conversation::class, 2);
        $this->assertCount(2, $user->conversations);

        $conversation = Conversation::first();
        $conversation->leftBy($user);
        $this->assertCount(1, $user->fresh()->conversations);
    }

    /** @test */
    public function a_hidden_conversation_reappears_when_a_new_message_is_added_to_conversation()
    {
        $user = $this->signIn();
        createMany(Conversation::class, 2);

        $this->assertCount(2, $user->conversations);

        $conversation = Conversation::first();
        $conversation->hideFrom($user);
        $this->assertCount(1, $user->fresh()->conversations);

        $conversation->addMessage('some message');
        $this->assertCount(2, $user->fresh()->conversations);

        $conversation->hideFrom($user);
        $this->assertCount(1, $user->fresh()->conversations);

        $conversation->unhide();
        $this->assertCount(2, $user->fresh()->conversations);
    }

    /** @test */
    public function a_conversation_knows_which_participants_are_active()
    {
        $conversationStarter = $this->signIn();
        $participantB = create(User::class);

        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants(array($participantB->name))
            ->create();

        $conversation->leftBy($participantB);

        $this->assertCount(2, $conversation->participants);
        $this->assertCount(1, $conversation->activeParticipants);
    }

}