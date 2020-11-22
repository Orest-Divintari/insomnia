<?php

namespace Tests\Feature\Comments;

use App\ProfilePost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCommentTest extends TestCase
{

    use RefreshDatabase;

    protected $bodyErrorMessage = 'Please enter a valid message.';
    /** @test */
    public function guests_cannot_post_a_comment()
    {
        $post = create(ProfilePost::class);

        $this->post(route('api.comments.store', $post), [])
            ->assertRedirect('login');
    }

    /** @test */
    public function unferified_authenticated_users_cannot_post_comments()
    {
        $user = create(User::class, [
            'email_verified_at' => null,
        ]);

        $this->signIn($user);

        $post = create(ProfilePost::class);

        $this->post(route('api.comments.store', $post), [])
            ->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function authenticated_users_that_have_verified_the_email_can_post_comments()
    {
        $user = $this->signIn();

        $post = create(ProfilePost::class);

        $comment = ['body' => 'some body'];

        $this->post(route('api.comments.store', $post), $comment);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $post->id,
            'repliable_type' => ProfilePost::class,
            'body' => $comment['body'],
            'user_id' => $user->id,
        ]);

    }

    /** @test */
    public function a_comment_requires_a_body()
    {
        $user = $this->signIn();

        $post = create(ProfilePost::class);

        $comment = ['body' => ''];

        $this->post(route('api.comments.store', $post), $comment)
            ->assertSessionHasErrors(['body' => $this->bodyErrorMessage]);

        $this->assertDatabaseMissing('replies', [
            'repliable_id' => $post->id,
            'repliable_type' => ProfilePost::class,
            'body' => $comment['body'],
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function a_comment_must_be_of_type_string()
    {
        $user = $this->signIn();

        $post = create(ProfilePost::class);

        $notStringBody = array(5);
        $comment = ['body' => $notStringBody];

        $this->post(route('api.comments.store', $post), $comment)
            ->assertSessionHasErrors(['body' => $this->bodyErrorMessage]);
    }
}