<?php

namespace Tests\Feature;

use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StarConversationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_participant_can_mark_a_conversation_as_starred()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();

        $this->assertFalse($conversation->starred);

        $this->post(route('api.star-conversations.store', $conversation));

        $this->assertTrue($conversation->starred);
    }

    /** @test */
    public function a_participant_can_mark_a_convrsation_as_unstarred()
    {
        $conversationStarter = $this->signIn();
        $conversation = ConversationFactory::by($conversationStarter)->create();

        $this->assertFalse($conversation->starred);
        $this->post(route('api.star-conversations.store', $conversation));
        $this->assertTrue($conversation->starred);

        $this->delete(route('api.star-conversations.destroy', $conversation));
        $this->assertFalse($conversation->starred);
    }

}