<?php

namespace Tests\Browser\Threads;

use App\Category;
use App\Thread;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewThreadsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_user_can_view_the_pagination_buttons_of_the_last_three_pages_of_replies_of_a_thread_of_a_given_category()
    {
        $category = create(Category::class);
        $thread = ThreadFactory::inCategory($category)->create();
        $numberOfPages = 10;
        $threadBody = 1;
        $numberOfReplies = Thread::REPLIES_PER_PAGE * $numberOfPages - $threadBody;
        $thread->increment('replies_count', $numberOfReplies);

        $this->browse(function (Browser $browser) use ($category) {
            $browser->visit(route('category-threads.index', $category))
                ->assertSeeLink('8')
                ->assertSeeLink('9')
                ->assertSeeLink('10');
        });
    }
}