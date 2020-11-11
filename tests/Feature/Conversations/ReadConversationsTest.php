<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ReadConversationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_view_a_conversation()
    {
        $someRandomConversationSlug = 'asdf';
        $this->get(route('conversations.show', $someRandomConversationSlug))
            ->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_view_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();

        $notParticipant = $this->signIn();

        $this->get(route('conversations.show', $conversation))
            ->assertStatus(Response::HTTP_FORBIDDEN);

    }

    /** @test */
    public function authorised_users_can_view_a_conversation_and_the_associated_messages()
    {
        $conversationStarter = $this->signIn();
        $participant = create(User::class);
        $message = ['body' => 'some message'];

        $conversation = ConversationFactory::by($conversationStarter)
            ->withParticipants([$participant->name])
            ->withMessage($message['body'])
            ->create();

        $response = $this->getJson(route('conversations.show', $conversation))->json();
        $participants = $response['participants'];
        $messages = $response['messages']['data'][0];
        $returnedConversation = $response['conversation'];

        $this->assertEquals($conversationStarter->id, $participants[0]['id']);
        $this->assertEquals($participant->id, $participants[1]['id']);
        $this->assertEquals($message['body'], $messages['body']);
        $this->assertTrue(array_key_exists('likes_count', $messages));
        $this->assertTrue(array_key_exists('is_liked', $messages));
        $this->assertEquals($conversationStarter->id, $messages['poster']['id']);
        $this->assertEquals($conversation->id, $returnedConversation['id']);

    }

    /** @test */
    public function a_user_can_view_his_own_visible_conversations()
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

        $conversations = $this->getJson(
            route('conversations.index')
        )->json()['data'];

        $this->assertEquals(2, $conversations[0]['messages_count']);
        $this->assertEquals(2, $conversations[0]['participants_count']);
        $this->assertEquals($conversation->type, $conversations[0]['type']);
        $this->assertEquals($newMessage->id, $conversations[0]['recent_message']['id']);
        $this->assertEquals($conversationStarter->id, $conversations[0]['starter']['id']);
        $this->assertEquals($newMessage->poster->id, $conversations[0]['recent_message']['poster']['id']);

        $this->assertTrue(
            collect($conversations[0]['participants'])->pluck('id')
                ->every(function ($value, $key) use ($conversation) {
                    return $conversation
                        ->participants
                        ->pluck('id')
                        ->contains($value);
                })
        );
    }

    /** @test */
    public function a_user_will_not_see_the_hidden_conversations()
    {
        $user = $this->signIn();

        $participant = create(User::class);

        $hiddenConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();

        $visibleConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();

        $hiddenConversation->hideFrom($participant);

        $this->signIn($participant);
        $conversations = $this->getJson(
            route('conversations.index')
        )->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals(
            $visibleConversation->id,
            $conversations[0]['id']
        );
    }

    /** @test */
    public function a_user_will_not_see_the_conversations_that_has_left()
    {
        $user = $this->signIn();

        $participant = create(User::class);

        $leftConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();

        $visibleConversation = ConversationFactory::by($user)
            ->withParticipants(array($participant->name))
            ->create();

        $leftConversation->leftBy($participant);

        $this->signIn($participant);

        $conversations = $this->getJson(
            route('conversations.index')
        )->json()['data'];

        $this->assertCount(1, $conversations);
        $this->assertEquals(
            $visibleConversation->id,
            $conversations[0]['id']
        );
    }

    /** @test */
    public function a_conversation_is_marked_as_read_when_a_user_visits_the_conversation()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->get(
            route('conversations.show', $conversation)
        );

        $this->assertFalse($conversation->hasBeenUpdated);

        $participant = create(User::class);

        $conversation->addParticipants(array($participant->name));

        $this->signIn($participant);

        $this->assertTrue($conversation->hasBeenUpdated);

        $participant->readConversation($conversation);

        $this->assertFalse($conversation->hasBeenUpdated);

    }

    /** @test */
    public function an_authorized_user_can_mark_a_conversation_as_read()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->patch(route('read-conversations.update', $conversation))
            ->assertOk();

        $this->assertDatabaseHas('reads', [
            'readable_id' => $conversation->id,
            'readable_type' => 'App\Conversation',
            'user_id' => $conversationStarter->id,
            'read_at' => $conversation->reads()->latest()->first()->read_at,
        ]);

        $this->assertFalse($conversation->fresh()->hasBeenUpdated);
    }

    /** @test */
    public function guests_cannot_mark_a_conversation_as_read()
    {

        $conversation = create(Conversation::class);
        $this->patch(route('read-conversations.update', $conversation))
            ->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_conversation_as_read()
    {
        $conversationStarter = $this->signIn();

        $conversation = ConversationFactory::create();

        $this->assertTrue($conversation->hasBeenUpdated);

        $nonParticipant = $this->signIn();

        $this->patch(route('read-conversations.update', $conversation))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function guests_cannot_mark_a_conversation_as_unread()
    {
        $conversation = create(Conversation::class);

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->delete(route('read-conversations.destroy', $conversation))
            ->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_mark_a_conversation_as_unread()
    {
        $conversation = create(Conversation::class);

        $nonParticipant = $this->signIn();

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->delete(route('read-conversations.destroy', $conversation))
            ->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function authorized_users_can_mark_a_conversation_as_unread()
    {
        $conversationStarter = $this->signIn();

        $conversation = create(Conversation::class);

        $this->assertTrue($conversation->hasBeenUpdated);

        $this->patch(route('read-conversations.update', $conversation));

        $this->assertFalse($conversation->hasBeenUpdated);

        $this->delete(route('read-conversations.destroy', $conversation))
            ->assertOk();

        $this->assertTrue($conversation->hasBeenUpdated);
    }

}