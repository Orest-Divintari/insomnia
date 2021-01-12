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
        $popularReply->likedBy($orestis);
        $popularReply->likedBy($john);
        $unpopularReply->likedBy($john);

        $response = $this->getJson(route('threads.show', $thread) . "?sortByLikes=1");

        $this->assertEquals($popularReply->id, $response['data'][0]['id']);
        $this->assertEquals($unpopularReply->id, $response['data'][1]['id']);

    }
}