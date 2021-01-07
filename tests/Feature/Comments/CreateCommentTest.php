<?php

namespace Tests\Feature\Comments;

use App\Exceptions\PostThrottlingException;
use App\ProfilePost;
use App\Reply;
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

        $response = $this->post(route('api.comments.store', $post), []);

        $response->assertRedirect(route('verification.notice'));
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

        $response = $this->post(route('api.comments.store', $post), $comment);

        $response->assertSessionHasErrors(['body' => $this->bodyErrorMessage]);
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

        $response = $this->post(route('api.comments.store', $post), $comment);

        $response->assertSessionHasErrors(['body' => $this->bodyErrorMessage]);
    }

    /** @test */
    public function a_user_cannot_add_a_profile_post_comment_if_has_exceed_the_post_rate_limit()
    {
        $this->withoutExceptionHandling();
        $profilePost = create(ProfilePost::class);
        $user = $this->signIn();
        $errorMessage = 'You must wait';
        $this->expectException(PostThrottlingException::class);

        $this->post(
            route('api.comments.store', $profilePost),
            raw(Reply::class)
        );
        $response = $this->post(
            route('api.comments.store', $profilePost),
            raw(Reply::class)
        );

        $this->assertTrue(str_contains(
            $response->getContent(),
            $errorMessage
        ));

    }
}