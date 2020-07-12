<?php

namespace Tests\Feature;

use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyFiltersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_filter_replies_based_on_the_number_of_likes()
    {

        $this->signIn();

        $thread = create(Thread::class);
        $unpopularReply = $thread->addReply(raw(Reply::class));
        $popularReply = $thread->addReply(raw(Reply::class));

        $user = create(User::class);
        $anotherUser = create(User::class);

        $popularReply->likedBy($user->id);
        $popularReply->likedBy($anotherUser->id);

        $this->get(route('threads.show', $thread) . "?sortByLikes=1")
            ->assertSeeInOrder([
                $popularReply->body,
                $unpopularReply->body,
            ]);

    }
}