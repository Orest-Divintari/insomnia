<?php

namespace Tests\Feature\Search;

use App\ProfilePost;
use App\User;
use Carbon\Carbon;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Search\SearchProfilePostsTest;

class SearchProfilePostsTestWithoutSearchQueryTest extends SearchProfilePostsTest
{
    use RefreshDatabase;

    /** @test */
    public function get_the_profile_posts_that_are_created_by_a_given_username()
    {
        $user = create(User::class);
        $desiredProfilePost = ProfilePostFactory::by($user)->create();
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
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_comments_that_a_given_username_has_posted()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $desiredProfilePost = create(ProfilePost::class);
        $user = create(User::class);
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();

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
        $this->assertContainsComment($results, $desiredComment);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_are_posted_by_a_given_username()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $user = create(User::class);
        $desiredProfilePost = ProfilePostFactory::by($user)->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();

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
        $this->assertContainsProfilePost($results, $desiredProfilePost);
        // $this->assertContainsComment($results, $desiredComment);

        $undesiredProfilePost->delete();
        $desiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_are_posted_on_a_user_profile_given_a_search_query()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $profileOwner = create(User::class);
        $desiredProfilePost = ProfilePostFactory::toProfile($profileOwner)
            ->withBody($this->searchTerm)
            ->create();
        $desiredComment = CommentFactory::withBody($this->searchTerm)
            ->toProfilePost($desiredProfilePost)
            ->create();

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
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_are_posted_on_a_user_profile_by_a_given_member()
    {
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        $user = create(User::class);
        $profileOwner = create(User::class);
        $desiredProfilePost = ProfilePostFactory::by($user)
            ->toProfile($profileOwner)
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();

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
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_by_a_given_user()
    {
        $user = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        Carbon::setTestNow();

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
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_by_a_given_user_name()
    {
        $user = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)->create();
        $anotherUndesiredComment = CommentFactory::by($user)
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        Carbon::setTestNow();

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
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_on_a_given_user_profile_by_a_given_user()
    {
        $user = create(User::class);
        $profileOwner = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)
            ->toProfile($profileOwner)
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $undesiredProfilePost = create(ProfilePost::class);
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $anotherUndesiredComment = CommentFactory::toProfilePost($anotherUndesiredProfilePost)->create();
        Carbon::setTestNow();

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
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }

    /** @test */
    public function get_the_profile_posts_and_comments_that_were_created_the_last_given_number_of_days_by_a_given_username_on_a_given_user_profile()
    {
        $user = create(User::class);
        $profileOwner = create(User::class);
        $daysAgo = 5;
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo));
        $desiredProfilePost = ProfilePostFactory::by($user)
            ->toProfile($profileOwner)
            ->create();
        $desiredComment = CommentFactory::by($user)
            ->toProfilePost($desiredProfilePost)
            ->create();
        $anotherUser = create(User::class);
        $undesiredProfilePost = ProfilePostFactory::toProfile($profileOwner)->create();
        $undesiredComment = CommentFactory::toProfilePost($undesiredProfilePost)->create();
        Carbon::setTestNow(Carbon::now()->subDays($daysAgo * 2));
        $anotherUndesiredProfilePost = ProfilePostFactory::by($user)
            ->toProfile($profileOwner)
            ->create();
        $anotherUndesiredComment = CommentFactory::by($user)
            ->toProfilePost($anotherUndesiredProfilePost)
            ->create();
        Carbon::setTestNow();

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
        $this->assertContainsComment($results, $desiredComment);
        $this->assertContainsProfilePost($results, $desiredProfilePost);

        $desiredProfilePost->delete();
        $undesiredProfilePost->delete();
        $anotherUndesiredProfilePost->delete();
    }

}