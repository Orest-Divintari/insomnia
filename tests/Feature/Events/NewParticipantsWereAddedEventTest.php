<?php

namespace Tests\Feature\Events;

use App\Listeners\Conversation\MarkConversationAsUnread;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class NewParticipantsWereAddedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_conversation_is_created_the_conversation_starter_is_added_as_a_participant_and_mark_conversation_unread_event_is_fired()
    {
        $conversationStarter = $this->signIn();
        $listener = Mockery::spy(MarkConversationAsUnread::class);
        app()->instance(MarkConversationAsUnread::class, $listener);

        $conversation = create(Conversation::class);

        $listener->shouldHaveReceived('handle')
            ->with(Mockery::on(function ($event) use ($conversation, $conversationStarter) {
                return $event->conversation->id == $conversation->id
                && $event->participantIds->contains($conversationStarter->id);
            }));

    }
    /** @test */
    public function when_a_new_participant_is_added_to_conversation_mark_the_conversation_as_unread_event_is_fired()
    {
        $this->signIn();
        $participant = create(User::class);
        $conversation = create(Conversation::class);
        $listener = Mockery::spy(MarkConversationAsUnread::class);
        app()->instance(MarkConversationAsUnread::class, $listener);

        $conversation->addParticipants([$participant->name]);

        $listener->shouldHaveReceived('handle')
            ->with(Mockery::on(function ($event) use ($conversation, $participant) {
                return $event->conversation->id == $conversation->id
                && $event->participantIds->contains($participant->id);
            }));
    }

}
