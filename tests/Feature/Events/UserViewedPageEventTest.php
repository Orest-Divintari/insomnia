<?php

namespace Tests\Feature\Events;

use App\Events\Activity\UserViewedPage;
use App\Listeners\Activity\LogUserActivity;
use App\Models\Category;
use App\Models\Thread;
use App\Models\User;
use Facades\Tests\Setup\ConversationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class UserViewedPageEventTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_user_visits_a_thread_an_event_is_fired()
    {
        $listener = Mockery::spy(LogUserActivity::class);
        app()->instance(LogUserActivity::class, $listener);
        $thread = create(Thread::class);

        $this->get(route('threads.show', $thread));

        $listener->shouldHaveReceived('handle', function ($event) use ($thread) {
            return $event->subject->id == $thread->id
            && $event->description == UserViewedPage::THREAD;
        });
    }

    /** @test */
    public function when_a_user_visits_a_category_an_event_is_fired()
    {
        $listener = Mockery::spy(LogUserActivity::class);
        app()->instance(LogUserActivity::class, $listener);
        $category = create(Category::class);

        $this->get(route('categories.show', $category));

        $listener->shouldHaveReceived('handle', function ($event) use (
            $category
        ) {
            return $event->subject->id == $category->id
            && $event->description == UserViewedPage::CATEGORY;
        });
    }

    /** @test */
    public function when_a_user_visits_the_forum_an_event_is_fired()
    {
        $listener = Mockery::spy(LogUserActivity::class);
        app()->instance(LogUserActivity::class, $listener);

        $this->get(route('forum'));

        $listener->shouldHaveReceived('handle', function ($event) {
            return is_null($event->subject)
            && $event->description == UserViewedPage::FORUM;
        });
    }

    /** @test */
    public function when_an_authenticated_user_opens_a_conversation_an_event_is_fired()
    {
        $listener = Mockery::spy(LogUserActivity::class);
        app()->instance(LogUserActivity::class, $listener);
        $john = $this->signIn();
        $peter = create(User::class);
        $conversation = ConversationFactory::by($john)
            ->withParticipants([$peter->name])
            ->create();

        $this->get(route('conversations.show', $conversation));

        $listener->shouldHaveReceived('handle', function ($event) {
            return is_null($event->subject)
            && $event->description == UserViewedPage::CONVERSATION;
        });
    }

    /** @test */
    public function when_an_authenticated_user_views_the_list_of_conversations_an_event_is_fired()
    {
        $listener = Mockery::spy(LogUserActivity::class);
        app()->instance(LogUserActivity::class, $listener);
        $user = $this->signIn();

        $this->get(route('conversations.index'));

        $listener->shouldHaveReceived('handle', function ($event) {
            return is_null($event->subject)
            && $event->description == UserViewedPage::CONVERSATION;
        });
    }

}
