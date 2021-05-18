<?php

namespace Tests\Feature\Comments;

use App\Exceptions\PostThrottlingException;
use App\Http\Middleware\ThrottlePosts;
use App\ProfilePost;
use App\Reply;
use App\User;
use Facades\Tests\Setup\ProfilePostFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CreateCommentTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    protected $bodyErrorMessage = 'Please enter a valid message.';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([ThrottlePosts::class]);
    }

    /** @test */
    public function guests_cannot_post_a_comment()
    {
        $post = create(ProfilePost::class);

        $this->post(route('ajax.comments.store', $post), [])
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

        $response = $this->post(route('ajax.comments.store', $post), []);

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function authenticated_users_that_have_verified_the_email_can_post_comments()
    {
        $user = $this->signIn();
        $post = create(ProfilePost::class);
        $comment = ['body' => $this->faker->sentence];

        $this->post(route('ajax.comments.store', $post), $comment);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $post->id,
            'repliable_type' => ProfilePost::class,
            'body' => $comment['body'],
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function profile_owners_can_always_post_comments_on_their_profile()
    {
        $this->withoutExceptionHandling();
        $profileOwner = $this->signIn();
        $profileOwner->allowNoone('post_on_profile');
        $post = ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];

        $this->post(route('ajax.comments.store', $post), $comment);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $post->id,
            'repliable_type' => ProfilePost::class,
            'body' => $comment['body'],
            'user_id' => $profileOwner->id,
        ]);
    }

    /** @test */
    public function members_can_post_a_comment_if_the_profile_owner_allows_it()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowMembers('post_on_profile');
        $post = ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $visitor = $this->signIn();

        $this->post(route('ajax.comments.store', $post), $comment);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $post->id,
            'repliable_type' => ProfilePost::class,
            'body' => $comment['body'],
            'user_id' => $visitor->id,
        ]);
    }

    /** @test */
    public function a_member_cannot_post_a_comment_if_the_profile_owner_does_not_allow_it()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowNoone('post_on_profile');
        $post = ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $visitor = $this->signIn();

        $response = $this->post(route('ajax.comments.store', $post), $comment);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_member_can_post_a_comment_if_is_followed_by_the_profile_owner()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowFollowing('post_on_profile');
        $post = ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $visitor = $this->signIn();
        $profileOwner->follow($visitor);

        $this->post(route('ajax.comments.store', $post), $comment);

        $this->assertDatabaseHas('replies', [
            'repliable_id' => $post->id,
            'repliable_type' => ProfilePost::class,
            'body' => $comment['body'],
            'user_id' => $visitor->id,
        ]);
    }

    /** @test */
    public function a_member_cannot_post_a_comment_if_is_not_followed_by_the_profile_owner()
    {
        $profileOwner = $this->signIn();
        $profileOwner->allowFollowing('post_on_profile');
        $post = ProfilePostFactory::by($profileOwner)->toProfile($profileOwner)->create();
        $comment = ['body' => $this->faker->sentence];
        $visitor = $this->signIn();

        $response = $this->post(route('ajax.comments.store', $post), $comment);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /** @test */
    public function a_comment_requires_a_body()
    {
        $user = $this->signIn();
        $post = create(ProfilePost::class);
        $comment = ['body' => ''];

        $response = $this->post(route('ajax.comments.store', $post), $comment);

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

        $response = $this->post(route('ajax.comments.store', $post), $comment);

        $response->assertSessionHasErrors(['body' => $this->bodyErrorMessage]);
    }

    /** @test */
    public function a_user_cannot_add_a_profile_post_comment_if_has_exceed_the_post_rate_limit()
    {
        $this->withMiddleware([ThrottlePosts::class]);
        $this->withoutExceptionHandling();
        $profilePost = create(ProfilePost::class);
        $user = $this->signIn();
        $errorMessage = 'You must wait';
        $this->expectException(PostThrottlingException::class);

        $this->post(
            route('ajax.comments.store', $profilePost),
            raw(Reply::class)
        );
        $response = $this->post(
            route('ajax.comments.store', $profilePost),
            raw(Reply::class)
        );

        $this->assertTrue(str_contains(
            $response->getContent(),
            $errorMessage
        ));
    }
}