<?php

namespace Tests\Feature\Search;

use App\ProfilePost;
use App\Thread;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ReplyFactory;
use Tests\Feature\Search\SearchAllPostsTest;

class SearchAllPostsWithoutSearchQueryTest extends SearchAllPostsTest
{

    /** @test */
    public function get_all_posts_that_are_created_by_a_given_username()
    {
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $user = $this->signIn();
        $desiredThread = create(
            Thread::class,
            ['user_id' => $user->id]
        );
        $desiredReply = ReplyFactory::create([
            'user_id' => $user->id,
            'repliable_id' => $desiredThread->id,
        ]);
        $desiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );
        $desiredComment = CommentFactory::create([
            'user_id' => $user->id,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        $results = $this->search([
            'postedBy' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->makeAssertions(
            $results,
            $desiredThread,
            $desiredReply,
            $desiredProfilePost,
            $desiredComment
        );

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $desiredThread->delete();
        $desiredProfilePost->delete();
    }

    /** @test */
    public function get_all_posts_that_were_created_by_a_given_username_the_last_given_number_of_days()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);
        $desiredThread = create(
            Thread::class,
            ['user_id' => $user->id]
        );
        $desiredReply = ReplyFactory::create([
            'repliable_id' => $desiredThread->id,
            'user_id' => $user->id,
        ]);

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);
        $undesiredThread = create(Thread::class);
        $undesiredReply = ReplyFactory::create([
            'repliable_id' => $undesiredThread->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);

        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
            'user_id' => $user->id,
        ]);
        $anotherUndesiredThread = create(
            Thread::class,
            ['user_id' => $user->id]
        );
        $anotherUndesiredReply = ReplyFactory::create([
            'repliable_id' => $anotherUndesiredThread->id,
            'user_id' => $user->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'lastCreated' => $daysAgo,
            'postedBy' => $user->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->makeAssertions(
            $results,
            $desiredThread,
            $desiredReply,
            $desiredProfilePost,
            $desiredComment
        );

        $undesiredThread->delete();
        $undesiredProfilePost->delete();
        $desiredThread->delete();
        $desiredProfilePost->delete();
        $anotherUndesiredThread->delete();
        $anotherUndesiredProfilePost->delete();
    }
}