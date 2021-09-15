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

class SearchAllPostsWithoutSearchQueryTest extends TestCase
{

    use RefreshDatabase, SearchableTest;

    /** @test */
    public function get_all_posts_tshat_are_created_by_a_given_username()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $user = $this->signIn();
        $desiredThread = ThreadFactory::by($user)->create();
        $desiredReply = ReplyFactory::by($user)
            ->toThread($desiredThread)
            ->create();
        $desiredProfilePost = ProfilePostFactory::by($user)->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $totalNumberOfDesiredItems = 4;

        $results = $this->searchJson([
            'posted_by' => $user->name,
        ],
            $totalNumberOfDesiredItems
        );

        $this->assertCount($totalNumberOfDesiredItems, $results);
        $this->assertContainsProfilePost($results, $desiredProfilePost);
        $this->assertContainsThread($results, $desiredThread);
        $this->assertContainsThreadReply($results, $desiredReply);
        $this->assertContainsComment($results, $desiredComment);

        $this->emptyIndices();
    }

    /** @test */
    public function get_all_posts_given_multiple_usernames()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $john = create(User::class);
        $doe = create(User::class);
        $threadByJohn = ThreadFactory::by($john)->create();
        $threadReplyByJohn = ReplyFactory::by($john)
            ->toThread($threadByJohn)
            ->create();
        $threadByDoe = ThreadFactory::by($doe)->create();
        $threadReplyByDoe = ReplyFactory::by($doe)
            ->toThread($threadByDoe)
            ->create();
        $profilePostByDoe = ProfilePostFactory::by($doe)->create();
        $commentByDoe = CommentFactory::by($doe)
            ->toProfilePost($profilePostByDoe)
            ->create();
        $profilePostByJohn = ProfilePostFactory::by($john)->create();
        $commentByJohn = CommentFactory::by($john)
            ->toProfilePost($profilePostByJohn)
            ->create();
        $usernames = $john->name . ',' . $doe->name;
        // the number of desired posts

        $numberOfDesiredItems = 8;
        $results = $this->searchJson([
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
    public function get_all_posts_that_were_created_by_a_given_username_the_last_given_number_of_days()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $desiredThread = ThreadFactory::by($user)->create();
        $desiredReply = ReplyFactory::by($user)
            ->toThread($desiredThread)
            ->create();
        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::toThread($undesiredThread)->create();
        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);

        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)->create();
        $anotherUndesiredComment = CommentFactory::by($user)
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        $anotherUndesiredThread = ThreadFactory::by($user)->create();
        $anotherUndesiredReply = ReplyFactory::by($user)
            ->toThread($anotherUndesiredThread)
            ->create();
        $totalNumberOfDesiredItems = 4;

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->searchJson([
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