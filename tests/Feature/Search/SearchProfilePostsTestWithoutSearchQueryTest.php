<?php

namespace Tests\Feature\Search;

use App\ProfilePost;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Search\SearchProfilePostsTest;

class SearchProfilePostsTestWithoutSearchQueryTest extends SearchProfilePostsTest
{
    use RefreshDatabase;

    /** @test */
    public function get_the_profile_posts_that_are_created_by_a_given_username()
    {
        $user = $this->signIn();
        $desiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );
        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(ProfilePost::class);

        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
        ],
            $this->numberOfDesiredProfilePosts
        );

        $this->assertCount(
            $this->numberOfDesiredProfilePosts, $results
        );
        $first = $this->numberOfDesiredProfilePosts - 1;
        $this->assertProfilePost($results[$first], $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_comments_that_a_given_username_has_posted()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $desiredProfilePost = create(ProfilePost::class);
        $user = $this->signIn();
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
        ],
            $this->numberOfDesiredComments
        );

        $this->assertCount(
            $this->numberOfDesiredComments,
            $results
        );
        $first = $this->numberOfDesiredComments - 1;
        $this->assertComment(
            $results[$first],
            $desiredComment,
            $desiredProfilePost
        );

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_are_posted_by_a_given_username()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $user = $this->signIn();
        $desiredProfilePost = create(
            ProfilePost::class,
            ['user_id' => $user->id]
        );
        $desiredComment = CommentFactory::create([
            'user_id' => $user->id,
            'repliable_id' => $desiredProfilePost->id,
        ]);

        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
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

        $this->assertProfilePost(
            $resultedProfilePost,
            $desiredProfilePost
        );
        $this->assertComment(
            $resultedComment,
            $desiredComment,
            $desiredProfilePost
        );

        $undesiredProfilePost->delete();
        $desiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_are_posted_on_a_user_profile_given_a_search_query()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $user = $this->signIn();
        $profileOwner = create(User::class);
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'profile_owner_id' => $profileOwner->id,
                'body' => $this->searchTerm,
            ]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
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
            $this->totalNumberOfDesiredItems, $results
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
    public function get_the_profile_posts_and_comments_that_are_posted_on_a_user_profile_by_a_given_member()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        $user = $this->signIn();
        $profileOwner = create(User::class);
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'profile_owner_id' => $profileOwner->id,
                'user_id' => $user->id,
            ]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
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
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_by_a_given_user()
    {
        $user = $this->signIn();
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(ProfilePost::class, ['user_id' => $user->id]);
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
            'lastCreated' => $daysAgo,
        ],
            $this->numberOfDesiredItems
        );

        $this->assertCount(
            $this->numberOfDesiredItems,
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
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_by_a_given_user_name()
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

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
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

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'lastCreated' => $daysAgo,
            'postedBy' => $user->name,
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

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_on_a_given_user_profile_by_a_given_user()
    {
        $user = $this->signIn();
        $profileOwner = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'profile_owner_id' => $profileOwner->id,
                "user_id" => $user->id,
            ]
        );

        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $profileOwner->id]
        );
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
            'postedBy' => $user->name,
            'lastCreated' => $daysAgo,
            'profileOwner' => $profileOwner->name,
        ],
            $this->totalNumberOfDesiredItems
        );

        $this->assertCount(
            $this->totalNumberOfDesiredItems, $results
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

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_by_a_given_username_on_a_given_user_profile()
    {
        $user = $this->signIn();
        $profileOwner = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
            ]
        );
        $desiredComment = CommentFactory::create([
            'repliable_id' => $desiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        $anotherUser = $this->signIn();
        $undesiredProfilePost = create(
            ProfilePost::class,
            ['profile_owner_id' => $profileOwner->id]
        );
        $undesiredComment = CommentFactory::create([
            'repliable_id' => $undesiredProfilePost->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo));
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $this->signIn($user);
        $anotherUndesiredProfilePost = create(
            ProfilePost::class,
            [
                'user_id' => $user->id,
                'profile_owner_id' => $profileOwner->id,
            ]
        );
        $anotherUndesiredComment = CommentFactory::create([
            'repliable_id' => $anotherUndesiredProfilePost->id,
            'user_id' => $user->id,
        ]);

        Carbon::setTestNow(Carbon::now()->addDays($daysAgo * 2));
        $results = $this->search([
            'type' => 'profile_post',
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