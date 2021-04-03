<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class MarkConversationAsReadOrUnreadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_mark_a_conversation_as_read()
    {
        $conversation = create(Conversation::class);

        $response = $this->patch(
            route('ajax.read-conversations.update', $conversation)
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_conversation_as_read()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $nonParticipant = $this->signIn();

        $response = $this->patch(
            route('ajax.read-conversations.update', $conversation)
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function guests_cannot_mark_a_conversation_as_unread()
    {
        $conversation = create(Conversation::class);

        $response = $this->delete(
            route('ajax.read-conversations.destroy', $conversation)
        );

        $response->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_conversation_as_unread()
    {
        $conversation = create(Conversation::class);
        $nonParticipant = $this->signIn();

        $response = $this->delete(
            route('ajax.read-conversations.destroy', $conversation)
        );

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function conversation_participants_can_mark_a_conversation_as_unread()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $this->assertFalse($conversation->hasBeenUpdated());

        $this->delete(
            route('ajax.read-conversations.destroy', $conversation)
        );
        $this->assertTrue($conversation->fresh()->hasBeenUpdated());
    }

    /** @test */
    public function a_conversation_participant_can_mark_a_conversation_as_read()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();
        $conversation->unread($conversationStarter);
        $this->assertTrue($conversation->hasBeenUpdated());

        $this->patch(
            route('ajax.read-conversations.update', $conversation)
        );

        $this->assertFalse($conversation->hasBeenUpdated());
    }

    /** @test */
    public function when_the_conversation_starter_creates_a_new_conversation_it_is_marked_as_read_for_the_conversation_starter()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);

        $this->post(
            route('conversations.store'),
            [
                'title' => 'some title',
                'message' => 'some message',
                'participants' => $participant->name,
            ]
        );

        $conversation = $conversationStarter->conversations()->first();
        $this->assertFalse($conversation->fresh()->hasBeenUpdated());
    }

    /** @test */
    public function when_a_participant_adds_a_new_message_to_the_conversation_the_conversation_is_marked_as_read_for_the_user_itself()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])
            ->by($conversationStarter)
            ->create();
        $this->signIn($participant);

        $this->post(
            route('ajax.messages.store', $conversation),
            ['body' => 'new message']
        );

        $this->assertFalse($conversation->fresh()->hasBeenUpdated());
    }

    /** @test */
    public function it_is_marked_as_unread_when_another_participant_adds_a_new_message_to_the_conversation()
    {
        $this->withoutExceptionHandling();
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $conversation = ConversationFactory::withParticipants([$participant->name])
            ->by($conversationStarter)
            ->create();
        $conversation->read($participant);
        $this->signIn($participant);
        $this->assertFalse($conversation->fresh()->hasBeenUpdated());
        $this->signIn($conversationStarter);
        Carbon::setTestNow(Carbon::now()->addMinute());

        $this->post(
            route('ajax.messages.store', $conversation),
            ['body' => 'new message']
        );

        $this->signIn($participant);
        $this->assertTrue($conversation->fresh()->hasBeenUpdated());
    }

    /** @test */
    public function a_conversation_is_marked_as_read_when_the_conversation_is_visited()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();

        $this->get(route('conversations.show', $conversation));

        $this->assertFalse($conversation->fresh()->hasBeenUpdated());
    }

}