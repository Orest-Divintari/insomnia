<?php

namespace Tests\Feature;

use App\ProfilePost;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\SearchProfilePostsTest;

class AlgoliaSearchProfilePostsTest extends SearchProfilePostsTest
{
    use RefreshDatabase;

    /** @test */
    public function search_profile_posts_and_comments_given_a_search_term()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $desiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $desiredComment = CommentFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        $results = $this->search(
            [
                'type' => 'profile_post',
                'q' => $this->searchTerm,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );

        $results = collect($results);
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');

        $this->assertComment($resultedComment, $desiredComment, $desiredProfilePost);
        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_given_a_search_term()
    {
        $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $desiredComment = CommentFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredProfilePost = create(ProfilePost::class);
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search(
            [
                'type' => 'profile_post',
                'q' => $this->searchTerm,
                'lastCreated' => $daysAgo,
            ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');
        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment($resultedComment, $desiredComment, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_the_profile_posts_and_comments_that_were_posted_on_a_given_user_profile_given_a_search_term()
    {
        $profileOwner = create(User::class);
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'body' => $this->searchTerm,
                'profile_owner_id' => $profileOwner->id,
            ]
        );
        $desiredComment = CommentFactory::create([
            'body' => $this->searchTerm,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        $undesiredProfilePost = create(
            ProfilePost::class,
            ['body' => $this->searchTerm]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
            'body' => $this->searchTerm,
        ]);

        $results = $this->search([
            'type' => 'profile_post',
            'q' => $this->searchTerm,
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );
        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');
        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function search_the_profile_posts_and_comments_that_were_posted_on_a_given_user_profile_by_a_given_username_given_a_search_term_the_last_given_number_of_days()
    {
        $profileOwner = create(User::class);
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
                'body' => $this->searchTerm,
            ]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
            'body' => $this->searchTerm,
        ]);

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(
            ProfilePost::class,
            [
                'profile_owner_id' => $profileOwner->id,
                'body' => $this->searchTerm,
            ]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
                'body' => $this->searchTerm,
            ]
        );
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
            'user_id' => $user->id,
            'body' => $this->searchTerm,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'q' => $this->searchTerm,
            'lastCreated' => $daysAgo,
            'postedBy' => $user->name,
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems,
            $results
        );

        $results = collect($results);
        $resultedComment = $results->firstWhere('type', 'profile-post-comment');
        $resultedProfilePost = $results->firstWhere('type', 'profile-post');

        $this->assertProfilePost($resultedProfilePost, $desiredProfilePost);
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }
}