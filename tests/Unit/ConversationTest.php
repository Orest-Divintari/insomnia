<?php

namespace Tests\Unit;

use App\Conversation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_conversation_can_have_many_participants()
    {
        $participantA = $this->signIn();

        $conversation = create(Conversation::class);

        $participantB = create(User::class);

        $conversation->addParticipants($participantB->name);

        $this->assertCount(2, $conversation->participants);
    }

    /** @test */
    public function a_single_participant_might_be_added_to_conversation()
    {
        $this->signIn();
        $participant = create(User::class);

        $conversation = create(Conversation::class);

        $conversation->addParticipants($participant->name);

        $this->assertEquals(
            $conversation->id,
            $participant->conversations()->first()->id
        );
    }

    /** @test */
    public function multiple_participants_might_be_added_to_conversation()
    {
        $this->signIn();
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
        $this->withExceptionHandling();
        $this->signIn();
        $conversation = create(Conversation::class);

        $message = ['body' => 'some message'];

        $conversation->addMessage($message['body']);
        $this->assertCount(1, $conversation->messages);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $conversation->id,
            'repliable_type' => Conversation::class,
            'body' => $message['body'],
            'user_id' => auth()->id(),
        ]);
    }

}