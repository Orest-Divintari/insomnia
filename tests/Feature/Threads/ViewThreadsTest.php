<?php

namespace Tests\Feature\Threads;

use App\Category;
use App\Reply;
use App\Thread;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewThreadsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->thread = create('App\Thread');
    }

    /** @test */
    public function a_user_can_view_a_thread()
    {
        $response = $this->get(route('threads.show', $this->thread));

        $response->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_view_the_threads_associated_with_a_category()
    {
        $this->withoutExceptionHandling();
        $category = create(Category::class);
        $thread = create(Thread::class, ['category_id' => $category->id]);

        $response = $this->get(route('threads.index', $category));

        $response->assertSee($thread->title);
    }

    /** @test */
    public function a_user_can_view_the_paginated_threads_associated_with_a_category()
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
        $pageNumber = (int) ceil($numberOfRepliesBefore / Thread::REPLIES_PER_PAGE);

        $response = $this->get(route('api.replies.show', $reply));

        $response->assertSee($reply->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_username()
    {
        $uric = create(
            User::class,
            [
                'name' => 'uric',
            ]
        );
        $this->signIn($uric);
        $threadByUric = create(Thread::class, [
            'user_id' => $uric->id,
        ]);
        $anotherUser = create(User::class);
        $threadByAnotherUser = create(
            Thread::class,
            ['user_id' => $anotherUser->id]
        );

        $response = $this->get(route('filtered-threads.index') . "?postedBy={$uric->name}");

        $response->assertSee($threadByUric->title)
            ->assertDontSee($threadByAnotherUser->title);
    }

    /** @test */
    public function a_user_can_filter_the_threads_of_a_category_by_username()
    {
        $uric = create(
            User::class,
            [
                'name' => 'uric',
            ]
        );
        $category = create(Category::class);
        $this->signIn($uric);
        $threadByUric = create(Thread::class, [
            'user_id' => $uric->id,
            'category_id' => $category->id,
        ]);
        $anotherUser = create(User::class);
        $threadByAnotherUser = create(
            Thread::class,
            [
                'user_id' => $anotherUser->id,
                'category_id' => $category->id,

            ]
        );

        $response = $this->get(route('threads.index', $category) . "?postedBy={$uric->name}");

        $response->assertSee($threadByUric->title)
            ->assertDontSee($threadByAnotherUser->title);
    }

    /** @test */
    public function user_can_view_the_threads_ordered_by_date_of_creation_in_descending_order()
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
    public function user_can_view_the_threads_of_a_category_ordered_by_date_of_creation_in_descending_order()
    {
        $category = create(Category::class);
        $this->thread->update(['category_id' => $category->id]);
        $oldThread = create(Thread::class, [
            'created_at' => Carbon::now()->subDay(),
            'id' => 10,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson(route('threads.index', $category) . '?newThreads=1');

        $this->assertEquals($this->thread->id, $response['data'][0]['id']);
        $this->assertEquals($oldThread->id, $response['data'][1]['id']);
    }

    /** @test */
    public function user_can_fetch_the_threads_with_the_most_recent_replies()
    {
        $recentlyActiveThread = $this->thread;
        $recentlyActiveThread->addReply(raw(Reply::class));
        Carbon::setTestNow(Carbon::now()->subDay());
        $inactiveThread = create(Thread::class);
        $inactiveThread->addReply(raw(Reply::class));
        Carbon::setTestNow(Carbon::now()->addDay());
        
        $threads = $this->getJson(
            route('filtered-threads.index') . "?newPosts=1"
        )->json()['data'];

        $this->assertCount(2, $threads);
        $this->assertEquals($recentlyActiveThread->id, $threads[0]['id']);
        $this->assertEquals($inactiveThread->id, $threads[1]['id']);
    }

    /** @test */
    public function user_can_fetch_the_threads_of_a_category_with_the_most_recent_replies()
    {
        $category = create(Category::class);
        $this->thread->update(['category_id' => $category->id]);
        $recentlyActiveThread = $this->thread;
        $recentlyActiveThread->addReply(raw(Reply::class));
        Carbon::setTestNow(Carbon::now()->subDay());
        $inactiveThread = create(Thread::class, ['category_id' => $category->id]);
        $inactiveThread->addReply(raw(Reply::class));
        Carbon::setTestNow(Carbon::now()->addDay());

        $threads = $this->getJson(
            route('threads.index', $category) . "?newPosts=1"
        )->json()['data'];

        $this->assertCount(2, $threads);
        $this->assertEquals($recentlyActiveThread->id, $threads[0]['id']);
        $this->assertEquals($inactiveThread->id, $threads[1]['id']);
    }

    /** @test */
    public function a_user_can_view_the_threads_that_has_replied_to()
    {
        $anotherUser = $this->signIn();
        $threadWithoutParticipation = create(Thread::class);
        $threadWithoutParticipation->addReply(
            raw(Reply::class, [
                'user_id' => $anotherUser,
            ])
        );
        $threadWithoutReplies = create(Thread::class);
        $user = create(User::class, ['name' => 'orestis']);
        $this->signIn($user);
        $thread = create(Thread::class);
        $thread->addReply(
            raw(Reply::class, [
                'user_id' => $user->id,
                'created_at' => Carbon::now()->addMinute(),
            ]));

        $response = $this->getJson(
            route('filtered-threads.index') . "?contributed=" . $user->name
        )->json();

        $this->assertEquals(
            $user->id,
            $response['data'][0]['recent_reply']['poster']['id']
        );
    }

    /** @test */
    public function a_user_can_view_the_threads_of_a_category_that_has_replied_to()
    {
        $anotherUser = $this->signIn();
        $category = create(Category::class);
        $threadWithoutParticipation = create(Thread::class, ['category_id' => $category->id]);
        $threadWithoutParticipation->addReply(
            raw(Reply::class, [
                'user_id' => $anotherUser,
            ])
        );
        $threadWithoutReplies = create(Thread::class, ['category_id' => $category->id]);
        $user = create(User::class, ['name' => 'orestis']);
        $this->signIn($user);
        $thread = create(Thread::class, ['category_id' => $category->id]);
        $thread->addReply(
            raw(Reply::class, [
                'user_id' => $user->id,
                'created_at' => Carbon::now()->addMinute(),
            ]));

        $response = $this->getJson(
            route('filtered-threads.index') . "?contributed=" . $user->name
        )->json();

        $this->assertEquals(
            $user->id,
            $response['data'][0]['recent_reply']['poster']['id']
        );
    }

    /** @test */
    public function user_can_view_trending_threads()
    {
        $this->thread->addReply(raw(Reply::class));
        $this->thread->addReply(raw(Reply::class));
        $this->thread->addReply(raw(Reply::class));
        $this->thread->update(['views' => 50]);
        $lessTrendingThread = create(Thread::class);
        $lessTrendingThread->addReply(raw(Reply::class));
        $lessTrendingThread->update(['views' => 100]);

        $response = $this->getJson(
            route('filtered-threads.index') . "?trending=1"
        );

        $this->assertEquals(
            $this->thread->id,
            $response['data'][0]['id']
        );
        $this->assertEquals(
            $lessTrendingThread->id,
            $response['data'][1]['id']
        );
    }

    /** @test */
    public function user_can_view_the_trending_threads_of_a_category()
    {
        $category = create(Category::class);
        $this->thread->addReply(raw(Reply::class));
        $this->thread->addReply(raw(Reply::class));
        $this->thread->addReply(raw(Reply::class));
        $this->thread->update(['views' => 50, 'category_id' => $category->id]);
        $lessTrendingThread = create(Thread::class, ['category_id' => $category->id]);
        $lessTrendingThread->addReply(raw(Reply::class));
        $lessTrendingThread->update(['views' => 100]);

        $response = $this->getJson(
            route('threads.index', $category) . "?trending=1"
        );

        $this->assertEquals(
            $this->thread->id,
            $response['data'][0]['id']
        );
        $this->assertEquals(
            $lessTrendingThread->id,
            $response['data'][1]['id']
        );
    }

    /** @test */
    public function user_can_view_the_unanswered_threads()
    {
        $threadWithReplies = create(Thread::class);
        $threadWithReplies->addReply(raw(Reply::class));

        $response = $this->get(route('filtered-threads.index') . "?unanswered=1");

        $response->assertSee($this->thread->title)
            ->assertDontSee($threadWithReplies->title);
    }

    /** @test */
    public function user_can_view_the_unanswered_threads_of_a_category()
    {
        $category = create(Category::class);
        $threadWithReplies = create(Thread::class, ['category_id' => $category->id]);
        $threadWithReplies->addReply(raw(Reply::class));
        $this->thread->update(['category_id' => $category->id]);

        $response = $this->get(route('threads.index', $category) . "?unanswered=1");

        $response->assertSee($this->thread->title)
            ->assertDontSee($threadWithReplies->title);
    }

    /** @test */
    public function a_user_can_view_the_threads_tha_has_subscribred_to()
    {
        $threadThatHasntSubscribedTo = create(Thread::class);
        $user = create(User::class, [
            'name' => 'jorgo',
        ]);
        $this->signIn($user);
        $this->thread->subscribe($user->id);

        $response = $this->get(route('filtered-threads.index') . "?watched=1");

        $response->assertSee($this->thread->title)
            ->assertDontSee($threadThatHasntSubscribedTo->title);
    }

    /** @test */
    public function a_user_can_view_the_threads_of_a_category_tha_has_subscribred_to()
    {
        $category = create(Category::class);
        $threadThatHasntSubscribedTo = create(Thread::class, ['category_id' => $category->id]);
        $user = create(User::class, [
            'name' => 'jorgo',
        ]);
        $this->signIn($user);
        $this->thread->subscribe($user->id);
        $this->thread->update(['category_id' => $category->id]);

        $response = $this->get(route('threads.index', $category) . "?watched=1");

        $response->assertSee($this->thread->title)
            ->assertDontSee($threadThatHasntSubscribedTo->title);
    }

    /** @test */
    public function user_can_view_the_threads_that_have_been_last_updated_X_days_ago()
    {
        $todaysThread = create(Thread::class);
        $oldThread = create(Thread::class, [
            'updated_at' => Carbon::now()->subMonth(),
        ]);
        $daysAgo = 3;
        $lastUpdated = Carbon::now()->subDays($daysAgo);

        $response = $this->get(
            route('filtered-threads.index') . "?lastUpdated=" . $daysAgo
        );

        $response->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);
    }

    /** @test */
    public function user_can_view_the_threads_of_a_category_that_have_been_last_updated_X_days_ago()
    {
        $category = create(Category::class);
        $todaysThread = create(Thread::class, ['category_id' => $category->id]);
        $oldThread = create(Thread::class, [
            'updated_at' => Carbon::now()->subMonth(),
            'category_id' => $category->id,
        ]);
        $daysAgo = 3;
        $lastUpdated = Carbon::now()->subDays($daysAgo);

        $response = $this->get(
            route('threads.index', $category) . "?lastUpdated=" . $daysAgo
        );

        $response->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);
    }

    /** @test */
    public function user_can_view_the_threads_that_have_been_last_created_X_days_ago()
    {
        $todaysThread = create(Thread::class);
        $oldThread = create(Thread::class, [
            'created_at' => Carbon::now()->subMonth(),
        ]);
        $daysAgo = 3;
        $lastUpdated = Carbon::now()->subDays($daysAgo);

        $response = $this->get(
            route('filtered-threads.index') . "?lastCreated=" . $daysAgo
        );

        $response->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);
    }

    /** @test */
    public function user_can_view_the_threads_of_a_category_that_have_been_last_created_X_days_ago()
    {
        $category = create(Category::class);
        $todaysThread = create(Thread::class, ['category_id' => $category->id]);
        $oldThread = create(Thread::class, [
            'created_at' => Carbon::now()->subMonth(),
            'category_id' => $category->id,
        ]);
        $daysAgo = 3;
        $lastUpdated = Carbon::now()->subDays($daysAgo);

        $response = $this->get(
            route('threads.index', $category) . "?lastCreated=" . $daysAgo
        );

        $response->assertSee($todaysThread->title)
            ->assertDontSee($oldThread->title);

    }

    /** @test */
    public function users_can_view_pinned_threads_if_exist()
    {
        createMany(Thread::class, 50);
        $pinnedThread = create(
            Thread::class,
            [
                'pinned' => true,
                'created_at' => Carbon::now()->subYear(),
                'updated_at' => Carbon::now()->subYear(),
            ],
            3,

        );

        $this->get(route('filtered-threads.index'))
            ->assertSee($pinnedThread->title);
    }

    /** @test */
    public function users_can_view_pinned_threads_of_a_category_if_exist()
    {
        $category = create(Category::class);
        createMany(Thread::class, 50, ['category_id' => $category->id]);
        $pinnedThread = create(
            Thread::class,
            [
                'pinned' => true,
                'created_at' => Carbon::now()->subYear(),
                'updated_at' => Carbon::now()->subYear(),
                'category_id' => $category->id,
            ],
            3,

        );

        $this->get(route('threads.index', $category))
            ->assertSee($pinnedThread->title);
    }
}