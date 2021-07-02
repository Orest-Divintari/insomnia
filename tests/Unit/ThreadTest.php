<?php

namespace Tests\Unit;

use App\Category;
use App\Read;
use App\Tag;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_thread_has_an_api_path()
    {
        $this->assertEquals(
            "/ajax/threads/{$this->thread->slug}",
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
        $user = create(User::class);
        $thread = ThreadFactory::by($user)->create();

        $this->assertInstanceOf(User::class, $thread->poster);
    }

    /** @test */
    public function a_thread_belongs_to_a_category()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();
        $this->assertInstanceOf(Category::class, $thread->category);
    }

    /** @test */
    public function a_thread_has_a_most_recent_reply()
    {
        $newReply = $this->thread->replies()->first();
        Carbon::setTestNow(Carbon::now()->subDay());
        $oldReply = ReplyFactory::toThread($this->thread)->create();
        Carbon::setTestNow();

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
        $thread = ThreadFactory::updatedAt(Carbon::now()->subMonth())->create();
        $reply = ReplyFactory::toThread($thread)->create();

        $this->assertEquals(
            $thread->fresh()->updated_at,
            $reply->updated_at
        );
    }

    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $thread = create(Thread::class);
        $user = create(User::class);

        $thread->addReply(['body' => $this->faker->sentence], $user);

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
    public function a_thread_can_be_marked_as_read_by_many_users()
    {
        $user = $this->signIn();
        $this->thread->read($user);
        $anotherUser = $this->signIn();
        $this->thread->read($anotherUser);

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

        $readThread->read($user);
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

    /** @test */
    public function get_the_threads_for_a_given_category()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();
        createMany(Thread::class, 5);

        $desiredThreads = Thread::forCategory($category)->get();

        $this->assertCount(1, $desiredThreads);
        $this->assertEquals($thread->id, $desiredThreads->first()->id);
    }

    /** @test */
    public function a_thread_knows_if_it_has_replies()
    {
        $thread = create(Thread::class);
        $this->assertFalse($thread->hasReplies());

        $thread->increment('replies_count');

        $this->assertTrue($thread->hasReplies());
    }

    /** @test */
    public function a_thread_knows_the_number_of_last_pages_of_replies()
    {
        $threadBody = 1;
        $numberOfRepliesForOnePage = Thread::REPLIES_PER_PAGE - $threadBody;
        $this->thread->update(['replies_count' => $numberOfRepliesForOnePage]);
        $this->assertEmpty(Thread::first()->lastPages);

        $pageNumberTwo = 2;
        $numberOfRepliesForTwoPages = Thread::REPLIES_PER_PAGE * 2 - $threadBody;
        $this->thread->update(['replies_count' => $numberOfRepliesForTwoPages]);
        $this->assertEquals(
            $this->thread->linkToPage($pageNumberTwo),
            Thread::first()->lastPages[$pageNumberTwo]
        );

        $pageNumberThree = 3;
        $numberOfRepliesForTwoPages = Thread::REPLIES_PER_PAGE * 3 - $threadBody;
        $this->thread->update(['replies_count' => $numberOfRepliesForTwoPages]);
        $this->assertEquals(
            $this->thread->linkToPage($pageNumberTwo),
            Thread::first()->lastPages[$pageNumberTwo]
        );
        $this->assertEquals(
            $this->thread->linkToPage($pageNumberThree),
            Thread::first()->lastPages[$pageNumberThree]
        );

        $pageNumberFour = 4;
        $numberOfRepliesFor4Pages = Thread::REPLIES_PER_PAGE * 4 - $threadBody;
        $this->thread->update(['replies_count' => $numberOfRepliesFor4Pages]);
        $this->assertEquals(
            $this->thread->linkToPage($pageNumberTwo),
            Thread::first()->lastPages[$pageNumberTwo]
        );
        $this->assertEquals(
            $this->thread->linkToPage($pageNumberThree),
            Thread::first()->lastPages[$pageNumberThree]
        );
        $this->assertEquals(
            $this->thread->linkToPage($pageNumberFour),
            Thread::first()->lastPages[$pageNumberFour]
        );

        $pageTen = 10;
        $numberOfRepliesFor10pages = Thread::REPLIES_PER_PAGE * 10 - $threadBody;
        $this->thread->update(['replies_count' => $numberOfRepliesFor10pages]);

        $this->assertEquals(
            $this->thread->linkToPage(8),
            Thread::first()->lastPages[8]
        );
        $this->assertEquals(
            $this->thread->linkToPage(9),
            Thread::first()->lastPages[9]
        );
        $this->assertEquals(
            $this->thread->linkToPage($pageTen),
            Thread::first()->lastPages[$pageTen]
        );
    }

    /** @test */
    public function a_thread_knows_when_the_authenticated_user_read_it()
    {
        $user = $this->signIn();
        $thread = create(Thread::class);
        $thread->read($user);

        $thread = Thread::withReadAt()->find($thread->id);

        $this->assertEquals(
            $thread->read_at,
            Carbon::parse($thread->reads->first()->read_at)->diffForHumans()
        );
    }

    /** @test */
    public function it_can_be_marked_as_read()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->assertTrue($thread->hasBeenUpdated());

        $thread->read($user);

        $this->assertFalse($thread->hasBeenUpdated());
    }

    /** @test */
    public function it_can_be_marked_as_unread()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $thread->read($user);
        $this->assertFalse($thread->hasBeenUpdated());

        $thread->unread($user);

        $this->assertTrue($thread->hasBeenUpdated());
    }

    /** @test */
    public function it_knows_if_a_user_is_a_subscriber()
    {
        $user = create(User::class);
        $thread = create(Thread::class);

        $thread->subscribe($user->id);

        $this->assertTrue($thread->hasSubscriber($user));
    }

    /** @test */
    public function it_knows_if_is_marked_as_ignored_by_visitor()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $thread->markAsIgnored($user);

        $thread = Thread::withIgnoredByVisitor($user)
            ->where('id', $thread->id)
            ->first();

        $this->assertTrue($thread->ignored_by_visitor);
    }

    /** @test */
    public function it_can_be_marked_as_ignored()
    {
        $john = $this->signIn();

        $this->thread->markAsIgnored($john);

        $this->assertTrue($this->thread->isIgnored($john));
    }

    /** @test */
    public function it_can_be_marked_as_unignored()
    {
        $john = $this->signIn();
        $this->thread->markAsIgnored($john);
        $thread = Thread::where('id', $this->thread->id)->first();

        $thread->markAsUnignored($john);

        $this->assertFalse($thread->isIgnored($john));
    }

    /** @test */
    public function it_knows_if_it_is_unignored()
    {
        $thread = create(Thread::class);
        $john = $this->signIn();

        $this->assertTrue($thread->isNotIgnored($john));
    }

    /** @test */
    public function it_includes_threads_that_are_directly_ignored()
    {
        $john = create(User::class);
        $this->thread->markAsIgnored($john);
        $this->signIn($john);

        $threads = Thread::all();

        $this->assertCount(1, $threads);
    }

    /** @test */
    public function it_includes_threads_that_are_created_by_ignored_users()
    {
        $john = create(User::class);
        $ignoredUser = $this->thread->poster;
        $ignoredUser->markAsIgnored($john);
        $this->signIn($john);

        $threads = Thread::all();

        $this->assertCount(1, $threads);
    }

    /** @test */
    public function it_determines_whether_it_is_ignored_by_the_authenticated_user_using_a_query_scope()
    {
        $john = $this->signIn();
        $this->thread->markAsIgnored($john);

        $thread = Thread::where('id', $this->thread->id)
            ->withIgnoredByVisitor($john)
            ->first();

        $this->assertTrue($thread->ignored_by_visitor);
    }

    /** @test */
    public function it_determines_whether_the_creator_of_the_thread_is_ignored_by_the_authenticated_user_using_a_query_scope()
    {
        $doe = create(User::class);
        $thread = ThreadFactory::by($doe)->create();
        $john = $this->signIn();
        $doe->markAsIgnored($john);

        $thread = Thread::where('id', $thread->id)
            ->withCreatorIgnoredByVisitor($john)
            ->first();

        $this->assertTrue($thread->creator_ignored_by_visitor);
    }
}