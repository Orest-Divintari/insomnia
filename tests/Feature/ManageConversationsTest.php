<?php

namespace Tests\Feature;

use App\Conversation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class ManageConversationsTest extends TestCase
{

    use RefreshDatabase;

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
    public function a_conversation_required_a_title_when_updated()
    {
        $conversationStarter = $this->signIn();

        $conversation = create(Conversation::class);

        $newTitle = ['title' => ''];

        $this->patch(
            route('api.conversations.update', $conversation),
            $newTitle
        )->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_converastion_title_must_be_string()
    {
        $conversationStarter = $this->signIn();

        $conversation = create(Conversation::class);

        $newTitle = ['title' => array(5)];

        $this->patch(
            route('api.conversations.update', $conversation),
            $newTitle
        )->assertSessionHasErrors('title');
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
}