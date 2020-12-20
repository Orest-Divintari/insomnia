<?php

namespace Tests\Feature\ThreadReplies;

use App\Like;
use App\Listeners\Subscription\NotifyReplyPoster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;
use \Facades\Tests\Setup\ReplyFactory;

class ReplyWasLikedEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_likes_a_thread_reply_then_an_event_is_fired()
    {
        $this->withExceptionHandling();
        $reply = ReplyFactory::create();
        $liker = $this->signIn();
        $listener = Mockery::spy(NotifyReplyPoster::class);
        app()->instance(NotifyReplyPoster::class, $listener);

        $this->post(route('api.likes.store', $reply));

        $like = Like::where('reply_id', $reply->id)->first();
        $listener->shouldHaveReceived('handle', function ($event) use (
            $like,
            $reply,
            $liker
        ) {
            return $event->thread->id == $reply->repliable->id
            && $event->reply->id == $reply->id
            && $event->liker->id == $liker->id
            && $event->like->id == $like->id;
        });

    }
}