<?php

namespace Tests\Feature;

use App\Http\Middleware\AppendVisitor;
use App\Thread;
use App\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppendVisitorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        $this->withMiddleware(AppendVisitor::class);
    }

    /** @test */
    public function it_appends_the_avatar_path_of_the_authenticated_user_on_every_json_request()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();

        $response = $this->getJson(route('ajax.conversations.index'))->json();

        $visitor = $response['visitor'];
        $this->assertEquals($visitor['avatar_path'], $user->avatar_path);
    }

    /** @test */
    public function it_appends_the_number_of_unread_converastions_on_every_json_request()
    {
        $this->withoutExceptionHandling();
        $user = $this->signIn();
        $conversation = ConversationFactory::by($user)->create();
        $conversation->unread($user);

        $response = $this->getJson(route('ajax.conversations.index'))->json();

        $visitor = $response['visitor'];
        $this->assertEquals($visitor['unread_conversations_count'], $user->unreadConversations()->count());
    }

    /** @test */
    public function it_appends_the_number_of_unviewed_notifications_on_every_json_request()
    {
        $orestis = create(User::class);
        $george = $this->signIn();
        $post = ['body' => $this->faker->sentence()];
        $this->postJson(route('ajax.profile-posts.store', $orestis), $post);
        $this->signIn($orestis);

        $response = $this->getJson(route('ajax.conversations.index'))->json();

        $visitor = $response['visitor'];

        $this->assertEquals($visitor['unviewed_notifications_count'], $orestis->unviewedNotificationsCount);
    }

    /** @test */
    public function it_appends_visitor_when_response_is_error()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);

        $response = $this->postJson(route('ajax.replies.store', $thread), ['body' => '']);

        $this->assertNotNull($response['data']);
        $this->assertNotNull($response['visitor']);
    }
}