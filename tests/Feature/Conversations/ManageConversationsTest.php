<?php

namespace Tests\Feature\Conversations;

use App\Conversation;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ManageConversationsTest extends TestCase
{

    use RefreshDatabase;

    protected $errorMessage = 'Please enter a valid title.';
    /** @test */
    public function the_title_of_the_conversation_can_be_updated_by_the_user_who_started_the_conversation()
    {
        $conversationStarter = $this->signIn();

        $conversation = create(Conversation::class);

        $newTitle = ['title' => 'some title'];

        $this->patch(
            route('api.conversations.update', $conversation),
            $newTitle
        );

        $this->assertEquals($conversation->fresh()->title, $newTitle['title']);
    }

    /** @test */
    public function a_conversation_requires_a_title_when_updated()
    {
        $conversationStarter = $this->signIn();

        $conversation = create(Conversation::class);

        $newTitle = ['title' => ''];

        $this->patchJson(
            route('api.conversations.update', $conversation),
            $newTitle
        )->assertStatus(422)
            ->assertJson(['title' => [$this->errorMessage]]);
    }

    /** @test */
    public function a_converastion_title_must_be_string()
    {
        $conversationStarter = $this->signIn();

        $conversation = create(Conversation::class);

        $newTitle = ['title' => array(5)];

        $this->patchJson(
            route('api.conversations.update', $conversation),
            $newTitle
        )->assertStatus(422)
            ->assertJson(['title' => [$this->errorMessage]]);
    }

    /** @test */
    public function unathorized_users_cannot_update_a_conversation()
    {
        $conversationStarter = $this->signIn();

        $conversation = create(Conversation::class);

        $unauthorizedUser = $this->signIn();

        $newTitle = ['title' => 'some title'];

        $this->patch(
            route('api.conversations.update', $conversation),
            $newTitle
        )->assertStatus(Response::HTTP_FORBIDDEN);

        $this->assertNotEquals($conversation->fresh()->title, $newTitle['title']);
    }

    /** @test */
    public function an_authorized_user_can_lock_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();

        $this->assertFalse($conversation->locked);

        $this->patch(
            route('api.conversations.update', $conversation),
            ['title' => $conversation->title, 'locked' => true]
        );

        $this->assertTrue($conversation->fresh()->locked);
    }

    /** @test */
    public function an_authorized_user_can_unlock_a_conversation()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();

        $this->assertFalse($conversation->locked);

        $this->patch(
            route('api.conversations.update', $conversation),
            ['title' => $conversation->title, 'locked' => true]
        );

        $this->assertTrue($conversation->fresh()->locked);

        $this->patch(
            route('api.conversations.update', $conversation),
            ['title' => $conversation->title, 'locked' => false]
        );

        $this->assertFalse($conversation->fresh()->locked);
    }
}