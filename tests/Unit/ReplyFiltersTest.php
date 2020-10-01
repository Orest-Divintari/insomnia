<?php

namespace Tests\Unit;

use App\Filters\ReplyFilters;
use App\Reply;
use Facades\Tests\Setup\ReplyFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyFiltersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function sort_the_replies_by_number_of_likes()
    {
        $replyWithoutLikes = ReplyFactory::create();
        $likedReply = ReplyFactory::create();
        $this->assertNotEquals($likedReply->id, Reply::first()->id);

        $user = $this->signIn();
        $likedReply->likedBy($user);

        $replyFilters = app(
            ReplyFilters::class,
            ['builder' => Reply::withCount('likes')]
        );
        $replyFilters->sortByLikes();
        $sortedReplies = $replyFilters->getBuilder()->get();
        $this->assertEquals($likedReply->id, $sortedReplies->first()->id);
    }
}