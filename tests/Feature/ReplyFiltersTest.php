<?php

namespace Tests\Feature;

use App\Thread;
use App\User;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReplyFiltersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_filter_replies_based_on_the_number_of_likes()
    {
        $thread = create(Thread::class);
        $unpopularReply = ReplyFactory::toThread($thread)->create();
        $popularReply = ReplyFactory::toThread($thread)->create();
        $orestis = create(User::class);
        $john = create(User::class);
        $this->signIn($orestis);
        $popularReply->like($orestis);
        $this->signIn($john);
        $popularReply->like($john);
        $unpopularReply->like($john);

        $response = $this->get(route('threads.show', [$thread, 'sort_by_likes' => true]));

        $response->assertSeeInOrder([$popularReply->body, $unpopularReply->body]);
    }
}
