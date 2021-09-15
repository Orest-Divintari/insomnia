<?php

namespace Tests\Feature\Search;

use App\Models\ProfilePost;
use App\Models\Thread;
use App\Models\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Facades\Tests\Setup\ReplyFactory;
use Facades\Tests\Setup\ThreadFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\SearchableTest;

class SearchAllPostsWithSearchQueryTest extends TestCase
{
    use RefreshDatabase, SearchableTest;

    /** @test */
    public function search_all_posts_given_a_search_term()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $desiredThread = ThreadFactory::withBody($this->sentence())->create();
        $desiredReply = ReplyFactory::toThread($desiredThread)
            ->withBody($this->sentence())
            ->create();
        $desiredProfilePost = ProfilePostFactory::withBody($this->sentence())->create();
        $desiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        $totalNumberOfDesiredItems = 4;

        $results = $this->searchJson(
            [
                'q' => $this->searchTerm(),
            ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount($totalNumberOfDesiredItems, $results);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
    }

    /** @test */
    public function search_all_posts_that_were_created_by_a_given_username_given_a_search_term()
    {
        $user = create(User::class);
        $desiredThread = ThreadFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $desiredReply = ReplyFactory::by($user)
            ->withBody($this->sentence())
            ->toThread($desiredThread)
            ->create();

        $desiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->sentence())
            ->create();

        $desiredComment = CommentFactory::by($user)
            ->withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        $undesiredComment = CommentFactory::withBody($this->sentence())->create();
        $anotherUser = create(User::class);
        $undesiredProfilePost = ProfilePostFactory::by($anotherUser)
            ->withBody($this->sentence())
            ->create();
        $undesiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($undesiredProfilePost)
            ->create();
        $undesiredThread = ThreadFactory::by($anotherUser)
            ->withBody($this->sentence())
            ->create();
        $undesiredReply = ReplyFactory::toThread($undesiredThread)
            ->create();
        $totalNumberOfDesiredItems = 4;

        $results = $this->searchJson([
            'q' => $this->searchTerm(),
            'posted_by' => $user->name,
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount($totalNumberOfDesiredItems, $results);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
    }

    /** @test */
    public function search_all_posts_that_were_created_by_multiple_users_given_a_search_term()
    {
        $undesiredComment = CommentFactory::withBody($this->sentence())->create();
        $undesiredProfilePost = ProfilePostFactory::withBody($this->sentence())
            ->create();
        $undesiredComment = CommentFactory::withBody($this->sentence())
            ->toProfilePost($undesiredProfilePost)
            ->create();
        $undesiredThread = ThreadFactory::withBody($this->sentence())
            ->create();
        $undesiredReply = ReplyFactory::toThread($undesiredThread)
            ->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)
            ->withBody($this->sentence())
            ->create();
        $threadReplyByJohn = ReplyFactory::by($john)
            ->withBody($this->sentence())
            ->toThread($threadByJohn)
            ->create();
        $threadByDoe = ThreadFactory::by($doe)
            ->withBody($this->sentence())
            ->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->withBody($this->sentence())
            ->toThread($threadByDoe)
            ->create();
        $profilePostByDoe = ProfilePostFactory::by($doe)
            ->withBody($this->sentence())
            ->create();
        $commentByDoe = CommentFactory::by($doe)
            ->withBody($this->sentence())
            ->toProfilePost($profilePostByDoe)
            ->create();
        $profilePostByJohn = ProfilePostFactory::by($john)
            ->withBody($this->sentence())
            ->create();
        $commentByJohn = CommentFactory::by($john)
            ->withBody($this->sentence())
            ->toProfilePost($profilePostByJohn)
            ->create();
        $usernames = $john->name . ',' . $doe->name;
        // the number of desired posts
        $numberOfDesiredItems = 8;

        $results = $this->searchJson([
            'q' => $this->searchTerm(),
            'posted_by' => $usernames,
        ],
            $numberOfDesiredItems
        );

        $this->assertContainsComment($results, $commentByDoe);
        $this->assertContainsComment($results, $commentByJohn);
        $this->assertContainsProfilePost($results, $profilePostByDoe);
        $this->assertContainsProfilePost($results, $profilePostByJohn);
        $this->assertContainsThread($results, $threadByDoe);
        $this->assertContainsThread($results, $threadByJohn);
        $this->assertContainsThreadReply($results, $threadReplyByDoe);
        $this->assertContainsThreadReply($results, $threadReplyByJohn);

        $this->emptyIndices();
    }

    /** @test */
    public function search_all_posts_given_a_search_term_and_username_that_were_created_the_last_given_number_of_days()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = ThreadFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $desiredReply = ReplyFactory::by($user)
            ->withBody($this->sentence())
            ->toThread($desiredThread)
            ->create();
        $desiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->withBody($this->sentence())
            ->toProfilePost($desiredProfilePost)
            ->create();
        $anotherUser = $this->signIn();
        $undesiredProfilePost = ProfilePostFactory::by($anotherUser)
            ->withBody($this->sentence())
            ->create();
        $undesiredComment = CommentFactory::by($anotherUser)
            ->withBody($this->sentence())
            ->toProfilePost($undesiredProfilePost)
            ->create();
        $undesiredThread = ThreadFactory::by($anotherUser)
            ->withBody($this->sentence())
            ->create();
        $undesiredReply = ReplyFactory::by($anotherUser)
            ->withBody($this->sentence())
            ->toThread($undesiredThread)
            ->create();
        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $undesiredComment = CommentFactory::by($user)
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        $anotherUndesiredThread = ThreadFactory::by($user)
            ->withBody($this->sentence())
            ->create();
        $anotherUndesiredReply = ReplyFactory::by($user)
            ->withBody($this->sentence())
            ->toThread($anotherUndesiredThread)
            ->create();
        $totalNumberOfDesiredItems = 4;

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->searchJson([
            'q' => $this->searchTerm(),
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount($totalNumberOfDesiredItems, $results);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $this->emptyIndices();
    }
}