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
use Tests\Feature\Search\SearchAllPostsTest;

class SearchAllPostsWithSearchQueryTest extends SearchAllPostsTest
{
    /** @test */
    public function search_all_posts_given_a_search_term()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $desiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $desiredReply = ReplyFactory::toThread($desiredThread)
            ->withBody($this->searchTerm)
            ->create();
        $desiredProfilePost = ProfilePostFactory::withBody($this->searchTerm)->create();
        $desiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();

        $results = $this->search(
            [
                'q' => $this->searchTerm,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount($this->totalNumberOfDesiredItems, $results);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $desiredThread->delete();
        $desiredProfilePost->delete();
    }

    /** @test */
    public function search_all_posts_that_were_created_by_a_given_username_given_a_search_term()
    {
        $user = create(User::class);
        $desiredThread = ThreadFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();
        $desiredReply = ReplyFactory::by($user)
            ->withBody($this->searchTerm)
            ->toThread($desiredThread)
            ->create();

        $desiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();

        $desiredComment = CommentFactory::by($user)
            ->withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $undesiredComment = CommentFactory::withBody($this->searchTerm)->create();
        $anotherUser = create(User::class);
        $undesiredProfilePost = ProfilePostFactory::by($anotherUser)
            ->withBody($this->searchTerm)
            ->create();
        $undesiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($undesiredProfilePost)
            ->create();
        $undesiredThread = ThreadFactory::by($anotherUser)
            ->withBody($this->searchTerm)
            ->create();
        $undesiredReply = ReplyFactory::toThread($undesiredThread)
            ->create();

        $results = $this->search([
            'q' => $this->searchTerm,
            'posted_by' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount($this->totalNumberOfDesiredItems, $results);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredThread->delete();
        $undesiredThread->delete();
        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_all_posts_that_were_created_by_multiple_users_given_a_search_term()
    {
        $undesiredComment = CommentFactory::withBody($this->searchTerm)->create();
        $undesiredProfilePost = ProfilePostFactory::withBody($this->searchTerm)
            ->create();
        $undesiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($undesiredProfilePost)
            ->create();
        $undesiredThread = ThreadFactory::withBody($this->searchTerm)
            ->create();
        $undesiredReply = ReplyFactory::toThread($undesiredThread)
            ->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)
            ->withBody($this->searchTerm)
            ->create();
        $threadReplyByJohn = ReplyFactory::by($john)
            ->withBody($this->searchTerm)
            ->toThread($threadByJohn)
            ->create();
        $threadByDoe = ThreadFactory::by($doe)
            ->withBody($this->searchTerm)
            ->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->withBody($this->searchTerm)
            ->toThread($threadByDoe)
            ->create();
        $profilePostByDoe = ProfilePostFactory::by($doe)
            ->withBody($this->searchTerm)
            ->create();
        $commentByDoe = CommentFactory::by($doe)
            ->withBody($this->searchTerm)
            ->toProfilePost($profilePostByDoe)
            ->create();
        $profilePostByJohn = ProfilePostFactory::by($john)
            ->withBody($this->searchTerm)
            ->create();
        $commentByJohn = CommentFactory::by($john)
            ->withBody($this->searchTerm)
            ->toProfilePost($profilePostByJohn)
            ->create();
        $usernames = $john->name . ',' . $doe->name;
        // the number of desired posts
        $numberOfDesiredItems = 8;

        $results = $this->search([
            'q' => $this->searchTerm,
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

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $threadByJohn->delete();
        $threadByDoe->delete();
        $profilePostByDoe->delete();
        $profilePostByJohn->delete();
    }

    /** @test */
    public function search_all_posts_given_a_search_term_and_username_that_were_created_the_last_given_number_of_days()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredThread = ThreadFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();
        $desiredReply = ReplyFactory::by($user)
            ->withBody($this->searchTerm)
            ->toThread($desiredThread)
            ->create();
        $desiredProfilePost = ProfilePostFactory::by($user)
            ->withBody($this->searchTerm)
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $anotherUser = $this->signIn();
        $undesiredProfilePost = ProfilePostFactory::by($anotherUser)
            ->withBody($this->searchTerm)
            ->create();
        $undesiredComment = CommentFactory::by($anotherUser)
            ->withBody($this->searchTerm)
            ->toProfilePost($undesiredProfilePost)
            ->create();
        $undesiredThread = ThreadFactory::by($anotherUser)
            ->withBody($this->searchTerm)
            ->create();
        $undesiredReply = ReplyFactory::by($anotherUser)
            ->withBody($this->searchTerm)
            ->toThread($undesiredThread)
            ->create();
        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::withBody($this->searchTerm)->create();
        $undesiredComment = CommentFactory::toProfilePost($anotherUndesiredProfilePost)->create();
        $anotherUndesiredThread = ThreadFactory::withBody($this->searchTerm)->create();
        $anotherUndesiredReply = ReplyFactory::withBody($this->searchTerm)
            ->toThread($anotherUndesiredThread)
            ->create();

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'q' => $this->searchTerm,
            'last_created' => $daysAgo,
            'posted_by' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount($this->totalNumberOfDesiredItems, $results);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredThread->delete();
        $undesiredThread->delete();
        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
        $anotherUndesiredThread->delete();
    }
}