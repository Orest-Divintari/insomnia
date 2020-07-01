<?php

namespace Tests\Unit;

use App\Like;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyTest extends TestCase
{

    use RefreshDatabase;
    /** @test */
    public function a_reply_belongs_to_a_thread()
    {
        $thread = create('App\Thread');
        $reply = create('App\Reply', ['repliable_id' => $thread->id, 'repliable_type' => Thread::class]);

        $this->assertInstanceOf(Thread::class, $reply->repliable);
    }

    /** @test */
    public function a_reply_belongs_to_the_user_who_posted_it()
    {
        $thread = create(Thread::class);
        $user = create(User::class);
        $reply = create(Reply::class, [
            'user_id' => $user->id,
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);
        $this->assertInstanceOf(User::class, $reply->fresh()->poster);
        $this->assertEquals($user->id, $reply->poster->id);

    }

    /** @test */
    public function a_reply_may_have_likes()
    {
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        Like::create([
            'reply_id' => $reply->id,
            'user_id' => 1,
        ]);

        $this->assertCount(1, $reply->likes);
    }

    /** @test */
    public function determine_whether_a_reply_has_been_liked()
    {
        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $like = Like::create([
            'reply_id' => $reply->id,
            'user_id' => 1,
        ]);
        $this->assertTrue($reply->isLiked);

    }

    /** @test */
    public function a_reply_can_be_liked_by_a_user()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $reply->likedBy($user->id);

        $this->assertCount(1, $reply->fresh()->likes);

    }

    /** @test */
    public function a_reply_can_be_unliked_by_a_user()
    {
        $user = $this->signIn();

        $thread = create(Thread::class);
        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $reply->likedBy($user->id);

        $this->assertCount(1, $reply->fresh()->likes);

        $reply->unlikedBy($user->id);

        $this->assertCount(0, $reply->fresh()->likes);

    }

    /** @test */
    public function a_reply_knows_in_which_page_it_belongs_to()
    {
        $thread = create(Thread::class);

        createMany(Reply::class, 30, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $reply = Reply::find(15);
        $correctPageNumber = ceil(15 / Reply::PER_PAGE);
        $this->assertEquals($correctPageNumber, $reply->pageNumber);
    }
}