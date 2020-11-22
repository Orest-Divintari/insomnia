<?php

namespace Tests\Feature\Messages;

use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateConversationMessageTest extends TestCase
{
    use RefreshDatabase;

    protected $errorMessage = 'Please enter a valid message.';

    /** @test */
    public function guests_cannot_add_a_message_to_a_conversation()
    {
        $conversationSlug = 'randomSlug';
        $this->post(route('api.messages.store', $conversationSlug), ['body' => 'some message'])
            ->assertRedirect('login');
    }

    /** @test */
    public function unathorized_users_cannot_add_messages_to_conversation()
    {
        $this->signIn();
        $conversation = ConversationFactory::create();
        $message = ['body' => 'some message'];
        $unathorizedUser = $this->signIn();
        $this->post(
            route('api.messages.store', $conversation),
            $message
        )->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_message_requires_a_body()
    {
        $this->signIn();
        $conversation = ConversationFactory::create();
        $message = ['body' => ''];

        $this->postJson(
            route('api.messages.store', $conversation),
            $message
        )->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function a_message_must_be_string()
    {
        $this->signIn();
        $conversation = ConversationFactory::create();
        $nonStringMessage = array(15);
        $message = ['body' => $nonStringMessage];

        $this->postJson(
            route('api.messages.store', $conversation),
            $message
        )->assertStatus(422)
            ->assertJson(['body' => [$this->errorMessage]]);
    }

    /** @test */
    public function participants_of_a_conversation_can_add_messages_to_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::create();

        $message = ['body' => 'some message'];

        $unathorizedUser = $this->signIn();
        $newMessage = $this->post(
            route('api.messages.store', $conversation),
            $message
        );

        $this->assertDatabaseHas('replies', [
            'repliable_type' => 'App\Conversation',
            'repliable_id' => $conversation->id,
            'user_id' => $conversationStarter->id,
            'body' => $message['body'],
        ]);

        $participant = create(User::class);

        $conversation->addParticipants([$participant->name]);
        $this->signIn($participant);

        $this->post(
            route('api.messages.store', $conversation),
            $message
        );

        $this->assertDatabaseHas('replies', [
            'repliable_type' => 'App\Conversation',
            'repliable_id' => $conversation->id,
            'user_id' => $participant->id,
            'body' => $message['body'],
        ]);
    }
}