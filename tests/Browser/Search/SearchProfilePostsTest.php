<?php

namespace Tests\Browser\Search;

use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SearchProfilePostsTest extends DuskTestCase
{
    use DatabaseMigrations, WithFaker;

    /** @test */
    public function authenticated_users_should_not_see_profile_posts_that_are_created_by_ignored_users()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $searchTerm = $this->faker()->sentence();
        $ingoredProfilePostBody = $searchTerm . ' ignored';
        $ignoredProfilePost = ProfilePostFactory::withBody($ingoredProfilePostBody)->by($doe)->create();
        $john->ignore($doe);

        $this->browse(function (Browser $browser) use ($ignoredProfilePost, $searchTerm, $john) {
            $response = $browser
                ->loginAs($john)
                ->pause(2500)
                ->visit(route('search.index', ['type' => 'profile_post', 'q' => $searchTerm]));

            $response->assertDontSee($ignoredProfilePost->body)
                ->assertVisible('@show-ignored-content-button');
        });
    }

    /** @test */
    public function authenticated_users_should_not_see_profile_post_comments_that_are_created_by_ignored_users()
    {
        $john = create(User::class);
        $doe = create(User::class);
        $searchTerm = $this->faker()->sentence();
        $ignoredCommentBody = $searchTerm . ' ignored';
        $ignoredComment = CommentFactory::withBody($ignoredCommentBody)->by($doe)->create();
        $john->ignore($doe);

        $this->browse(function (Browser $browser) use ($ignoredComment, $searchTerm, $john) {
            $response = $browser
                ->loginAs($john)
                ->pause(5000)
                ->visit(route('search.index', ['type' => 'profile_post', 'q' => $searchTerm]));

            $response->assertDontSee($ignoredComment->body)
                ->assertVisible('@show-ignored-content-button');
        });
    }
}