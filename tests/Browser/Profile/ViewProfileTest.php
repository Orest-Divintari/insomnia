<?php

namespace Tests\Browser\Profile;

use App\ProfilePost;
use App\User;
use Facades\Tests\Setup\CommentFactory;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ViewProfileTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        Notification::fake();
    }

    /** @test */
    public function view_the_user_profile_information()
    {
        $user = create(User::class);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit("/profiles/{$user->name}")
                ->assertSee($user->name)
                ->assertSee('Macrumors newbie')
                ->assertSee('Messages')
                ->assertSeeIn('@messages-count', 0)
                ->assertSee('Likes Score')
                ->assertSeeIn('@likes-count', 0)
                ->assertSee('Points')
                ->assertSeeIn('@points', 0)
                ->assertSee('Joined')
                ->assertSee($user->join_date)
                ->assertSeeLink($user->name);
        });
    }

    /** @test */
    public function view_profile_posts_and_associated_comments()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $post = ProfilePostFactory::toProfile($orestis)->by($john)->create();
        $comment = CommentFactory::by($john)->toProfilePost($post)->create();

        $this->browse(function (Browser $browser) use ($orestis, $john, $comment, $post) {
            $browser->visit("/profiles/{$orestis->name}")
                ->assertSee($post->body)
                ->assertSee($post->date_created)
                ->assertSee($comment->body)
                ->assertSee($comment->date_created)
                ->assertSee($john->name);
        });
    }

    /** @test */
    public function jump_to_a_specific_comment_in_profile_posts()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * 5);
        $lastPost = $posts->first();
        $comment = CommentFactory::by($john)->toProfilePost($lastPost)->create();

        $this->browse(function (Browser $browser) use ($orestis, $john, $comment, $lastPost) {
            $browser->visit(route('comments.show', $comment))
                ->assertSee($lastPost->body)
                ->assertSee($lastPost->date_created)
                ->assertSee($comment->body)
                ->assertSee($comment->date_created)
                ->assertSee($john->name)
                ->assertAttribute('@profile-post-comment', 'id', 'profile-post-comment-' . $comment->id);
        });
    }

    /** @test */
    public function jump_to_a_specific_profile_post()
    {
        $orestis = create(User::class);
        $john = create(User::class);
        $posts = ProfilePostFactory::by($john)
            ->toProfile($orestis)
            ->createMany(ProfilePost::PER_PAGE * 5);
        $lastPost = $posts->first();

        $this->browse(function (Browser $browser) use ($orestis, $lastPost, $john) {
            $browser->visit(route('profile-posts.show', $lastPost))
                ->assertSee($lastPost->body)
                ->assertSee($lastPost->date_created)
                ->assertSee($john->name)
                ->assertAttribute('@profile-post', 'id', 'profile-post-' . $lastPost->id);
        });
    }
}