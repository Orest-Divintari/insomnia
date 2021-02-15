<?php

namespace Tests\Feature\Activities;

use App\Activity;
use App\Category;
use App\Events\Activity\UserViewedPage;
use App\Thread;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnlineUserActivitiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function display_the_latest_viewed_activity_of_guest_online_users()
    {
        $thread = create(Thread::class);
        $this->get(route('threads.show', $thread));
        $this->get(route('forum'));
        $this->assertCount(2, Activity::typeViewed()->get());

        $response = $this->get(route('online-users-activity.index'));

        $response->assertSee('Guest');
        $response->assertSee(UserViewedPage::FORUM);
        $response->assertDontSee(UserViewedPage::THREAD);
    }

    /** @test */
    public function display_the_latest_viewed_activity_of_authenticated_online_users()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->get(route('threads.show', $thread));
        $this->get(route('forum'));
        $this->assertCount(2, Activity::typeViewed()->get());

        $response = $this->get(route('online-users-activity.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::FORUM);
        $response->assertDontSee(UserViewedPage::THREAD);
    }

    /** @test */
    public function when_a_user_visits_a_thread_the_activity_is_displayed()
    {
        $thread = create(Thread::class);
        $user = $this->signIn();
        $this->get(route('threads.show', $thread));

        $response = $this->get(route('online-users-activity.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::THREAD);
        $response->assertSee($thread->title);
    }

    /** @test */
    public function when_a_user_visits_a_category_the_activity_is_displayed()
    {
        $category = create(Category::class);
        $user = $this->signIn();
        $this->get(route('categories.show', $category));

        $response = $this->get(route('online-users-activity.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::CATEGORY);
        $response->assertSee($category->title);
    }

    /** @test */
    public function when_a_user_visits_the_list_of_conversations_the_activity_is_displayed()
    {
        $user = $this->signIn();
        $this->get(route('conversations.index'));

        $response = $this->get(route('online-users-activity.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::CONVERSATION);
    }

    /** @test */
    public function when_a_user_opens_a_conversation_the_activity_is_displayed()
    {
        $user = $this->signIn();
        $conversation = ConversationFactory::by($user)->create();
        $this->get(route('conversations.show', $conversation));

        $response = $this->get(route('online-users-activity.index'));

        $response->assertSee($user->name);
        $response->assertSee(UserViewedPage::CONVERSATION);
    }

}