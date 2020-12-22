<?php

namespace Tests\Unit;

use App\Category;
use App\Read;
use App\Reply;
use App\Tag;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_thread_has_an_api_path()
    {
        $this->assertEquals(
            "/api/threads/{$this->thread->slug}",
            $this->thread->api_path()
        );
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $this->assertCount(1, $this->thread->replies);

        create('App\Reply', [
            'repliable_id' => $this->thread->id,
            'repliable_type' => Thread::class,
        ]);

        $this->assertCount(2, $this->thread->fresh()->replies);

    }

    /** @test */
    public function a_thread_is_posted_by_a_user()
    {
        $user = create('App\User');
        $thread = create('App\Thread', ['user_id' => $user->id]);
        $this->assertInstanceOf(User::class, $thread->poster);
    }

    /** @test */
    public function a_thread_belongs_to_a_category()
    {
        $category = create(Category::class);
        $thread = create(Thread::class, ['category_id' => $category->id]);
        $this->assertInstanceOf(Category::class, $thread->category);
    }

    /** @test */
    public function a_thread_has_a_most_recent_reply()
    {

        $newReply = $this->thread->replies()->first();
        $oldReply = create(Reply::class, [
            'repliable_type' => Thread::class,
            'repliable_id' => $this->thread->id,
            'updated_at' => Carbon::now()->subDay(),
        ]);
        $thread = Thread::where('id', $this->thread->id)->withRecentReply()->first();

        $this->assertEquals($thread->recentReply->id, $newReply->id);

    }

    /** @test */
    public function thread_has_a_shorter_version_of_its_title()
    {
        $this->assertEquals(
            Str::limit($this->thread->title, Thread::TITLE_LENGTH, ''),
            $this->thread->shortTitle
        );
    }

    /** @test */
    public function a_thread_is_updated_when_a_new_reply_is_published()
    {
        $thread = create(Thread::class, [
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        $reply = create(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $this->assertEquals(
            $thread->fresh()->updated_at,
            $reply->updated_at
        );
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $thread = create(Thread::class);
        $reply = raw(Reply::class, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);

        $thread->addReply($reply);

        $this->assertEquals($thread->fresh()->replies_count, 1);

    }

    /** @test */
    public function a_thread_can_have_many_subscriptions()
    {
        $user = create(User::class);

        $thread = create(Thread::class);

        $this->assertCount(0, $thread->subscriptions);

        $thread->subscribe($user->id);

        $this->assertCount(1, $thread->fresh()->subscriptions);
    }

    /** @test */
    public function a_user_can_subscribe_to_a_thread()
    {
        $user = create(User::class);

        $thread = create(Thread::class);

        $this->assertCount(0, $thread->subscriptions);

        $thread->subscribe($user->id);

        $this->assertCount(1, $thread->fresh()->subscriptions);

    }

    /** @test */
    public function a_user_can_unsubscribe_from_a_thread()
    {
        $user = create(User::class);

        $thread = create(Thread::class);

        $this->assertCount(0, $thread->subscriptions);

        $thread->subscribe($user->id);

        $this->assertCount(1, $thread->fresh()->subscriptions);

        $thread->unsubscribe($user->id);

        $this->assertCount(0, $thread->fresh()->subscriptions);
    }

    /** @test */
    public function a_thread_can_determine_whether_a_the_autneticated_user_has_subscribed_to_it()
    {

        $thread = create(Thread::class);

        $user = $this->signIn();

        $this->assertCount(0, $thread->subscriptions);

        $thread->subscribe($user->id);

        $this->assertTrue($thread->fresh()->subscribedByAuthUser);

        $thread->unsubscribe($user->id);

        $this->assertFalse($thread->fresh()->subscribedByAuthUser);
    }

    /** @test */
    public function determine_whether_a_given_user_is_subscribed_to_a_thread()
    {
        $user = create(User::class);

        $this->assertFalse($this->thread->isSubscribedBy($user->id));

        $this->thread->subscribe($user->id);

        $this->assertTrue($this->thread->isSubscribedBy($user->id));
    }

    /** @test */
    public function a_thread_can_be_marked_as_read_by_many_users()
    {
        $user = $this->signIn();

        $user->read($this->thread);

        $anotherUser = $this->signIn();

        $anotherUser->read($this->thread);

        $this->assertCount(2, $this->thread->fresh()->reads);
    }

    /** @test */
    public function a_thread_can_be_locked()
    {
        $this->assertFalse($this->thread->locked);

        $this->thread->lock();

        $this->assertTrue($this->thread->locked);
    }

    /** @test */
    public function a_thread_can_be_unlocked()
    {
        $this->assertFalse($this->thread->locked);

        $this->thread->lock();
        $this->assertTrue($this->thread->locked);

        $this->thread->unlock();
        $this->assertFalse($this->thread->locked);
    }

    /** @test */
    public function a_thread_can_be_pinned()
    {
        $thread = create(Thread::class);
        $this->assertFalse($thread->pinned);

        $thread->pin();

        $this->assertTrue($thread->pinned);
    }

    /** @test */
    public function a_thread_can_be_unpinned()
    {
        $thread = create(Thread::class);
        $thread->pin();
        $this->assertTrue($thread->pinned);

        $thread->unpin();

        $this->assertFalse($thread->pinned);
    }

    /** @test */
    public function a_thread_can_have_many_tags()
    {
        $thread = create(Thread::class);
        $tag = create(Tag::class);

        $this->assertCount(0, $thread->tags);

        $thread->addTags([$tag->name]);

        $this->assertCount(1, $thread->fresh()->tags);
        $this->assertEquals($tag->id, $thread->tags()->first()->id);
    }

    /** @test */
    public function a_thread_knows_if_it_has_been_updated()
    {
        $threads = createMany(Thread::class, 5);
        $readThread = $threads->first();
        $user = $this->signIn();

        $user->read($readThread);
        $threads = Thread::withHasBeenUpdated()->get();
        $this->assertFalse(
            $threads->firstWhere('id', $readThread->id)->has_been_updated
        );
    }

    /** @test */
    public function get_the_pinned_threads()
    {
        $threads = createMany(Thread::class, 5);
        $thread = $threads->first();
        $thread->pin();

        $pinnedThread = Thread::pinned()->get();

        $this->assertCount(1, $pinnedThread);
        $this->assertEquals($pinnedThread->first()->id, $thread->id);
    }

}