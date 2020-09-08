<?php

namespace Tests\Feature;

use App\Category;
use App\Reply;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $this->get(route('threads.show', $this->thread))
            ->assertSee($this->thread->title);

    }

    /** @test */
    public function a_user_can_read_the_threads_associated_with_a_category()
    {
        $category = create(Category::class);

        $thread = create(Thread::class, ['category_id' => $category->id]);

        $this->get(route('threads.index', $category))
            ->assertSee($thread->title);
    }

    /** @test */
    public function a_user_can_read_the_paginated_threads_associated_with_a_category()
    {
        $category = create(Category::class);

        createMany(Thread::class, 100, [
            'category_id' => $category->id,
        ]);

        $response = $this->getJson(route('api.threads.index', $category))->json();
        $this->assertCount(Thread::PER_PAGE, $response['data']);

        $response = $this->getJson(route('api.threads.index', $category) . '?page=2')->json();
        $this->assertCount(Thread::PER_PAGE, $response['data']);
    }

    /** @test */
    public function a_user_can_go_to_a_specific_reply_on_a_paginated_thread()
    {
        $thread = create(Thread::class, ['replies_count' => 30]);

        createMany(Reply::class, 30, [
            'repliable_id' => $thread->id,
            'repliable_type' => Thread::class,
        ]);
        $reply = Reply::find(15);

        $numberOfRepliesBefore = $thread->replies()->where('id', '<=', $reply->id)->count();

        $pageNumber = (int) ceil($numberOfRepliesBefore / Reply::REPLIES_PER_PAGE);

        $this->get(route('api.replies.show', $reply))
            ->assertSee($reply->title);

    }

    /** @test */
    public function a_user_can_filter_threads_by_username()
    {
        $uric = create(User::class, [
            'name' => 'uric',
        ]);

        $this->signIn($uric);

        $threadByUric = create(Thread::class, [
            'user_id' => $uric->id,
        ]);

        $threadByAnotherUser = create(Thread::class);

        $this->get(route('filtered-threads.index') . "?startedBy={$uric->name}")
            ->assertSee($threadByUric->title)
            ->assertDontSee($threadByAnotherUser->title);

    }

    /** @test */
    public function user_can_read_the_threads_ordered_by_date_of_creation_in_descending_order()
    {
        $oldThread = create(Thread::class, [
            'created_at' => Carbon::now()->subDay(),
            'id' => 10,
        ]);

        $response = $this->getJson(route('filtered-threads.index') . '?newThreads=1');
        $this->assertEquals($this->thread->id, $response['data'][0]['id']);
        $this->assertEquals($oldThread->id, $response['data'][1]['id']);
    }

    /** @test */
    public function user_can_fetch_the_threads_with_the_most_recent_replies()
    {
        $newThreadWithReplies = create(Thread::class);
        $newThreadWithReplies->addReply(raw(Reply::class));

        $oldThreadWithReples = create(Thread::class, [
            'updated_at' => Carbon::now()->subDay(),
        ]);
        $oldThreadWithReples->addReply(raw(Reply::class));
        $threadWithNoReplies = $this->thread;

        $response = $this->getJson(route('filtered-threads.index') . "?newPosts=1");
        $this->assertCount(2, $response['data']);

    }

    /** @test */
    public function a_user_can_read_the_threads_that_has_replied_to()
    {
        $user = $this->signIn();

        $threadWithNoParticipation = create(Thread::class);

        $this->post(route('api.replies.store', $this->thread), ['body' => 'some random text']);

        $this->get(route('filtered-threads.index') . "?contributed=" . $user->name)
            ->assertSee($this->thread->title)
            ->assertDontSee($threadWithNoParticipation->title);

    }

    /** @test */
    public function user_can_read_trending_threads()
    {
        $this->thread->addReply(raw(Reply::class));
        $this->thread->addReply(raw(Reply::class));
        $this->thread->addReply(raw(Reply::class));
        $this->thread->update(['views' => 50]);

        $lessTrendingThread = create(Thread::class);
        $lessTrendingThread->addReply(raw(Reply::class));
        $lessTrendingThread->update(['views' => 100]);

        $response = $this->getJson(route('filtered-threads.index') . "?trending=1");
        $this->assertEquals($this->thread->id, $response['data'][0]['id']);
        $this->assertEquals($lessTrendingThread->id, $response['data'][1]['id']);
    }

    /** @test */
    public function user_can_read_the_unanswered_threads()
    {
        $threadWithReplies = create(Thread::class);
        $threadWithReplies->addReply(raw(Reply::class));

        $this->get(route('filtered-threads.index') . "?unanswered=1")
            ->assertSee($this->thread->title)
            ->assertDontSee($threadWithReplies->title);
    }

    /** @test */
    public function a_user_can_read_the_threads_tha_has_subscribred_to()
    {
        $threadThatHasntSubscribedTo = create(Thread::class);

        $user = create(User::class, [
            'name' => 'jorgo',
        ]);
        $this->signIn($user);

        $this->thread->subscribe($user->id);

        $this->get(route('filtered-threads.index') . "?watched=1")
            ->assertSee($this->thread->title)
            ->assertDontSee($threadThatHasntSubscribedTo->title);

    }

    /** @test */
    public function user_can_read_the_threads_that_have_been_last_updated_X_days_ago()
    {

        $todaysThread = create(Thread::class);

        $oldThread = create(Thread::class, [
            'updated_at' => Carbon::now()->subMonth(),
        ]);

        $numberOdDays = 3;
        $lastUpdated = Carbon::now()->subDays($numberOdDays);

        $this->get(route('filtered-threads.index') . "?lastUpdated=" . $numberOdDays)
            ->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);

    }

    /** @test */
    public function user_can_read_the_threads_that_have_been_last_crated_X_days_ago()
    {

        $todaysThread = create(Thread::class);

        $oldThread = create(Thread::class, [
            'created_at' => Carbon::now()->subMonth(),
        ]);

        $numberOdDays = 3;
        $lastUpdated = Carbon::now()->subDays($numberOdDays);

        $this->get(route('filtered-threads.index') . "?lastCreated=" . $numberOdDays)
            ->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);

    }

}